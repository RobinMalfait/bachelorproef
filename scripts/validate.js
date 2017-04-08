// Dependencies
const fs = require('fs');
const chalk = require('chalk');
const leftPad = require('left-pad');
const glob = require('glob-fs')({gitignore: true});
const promisify = require('es6-promisify');

// Setup
const FILES_TO_IGNORE = ['voorwoord.tex', 'structure.tex'];
const NOT_ALLOWED_WORDS = ['ik', 'je', 'jij', 'we', 'wij', 'jullie', 'onze'];

// Promisify functions
const readFile = promisify(fs.readFile);

// Define helper utils
const l = console.log;

const analyzeLines = (contents) => {
  const lines = contents.split('\n').reduce((result, line, index) => {
    // Strip lines which are comments
    if (line.indexOf('%%') === 0) {
      return result;
    }

    return [
      ...result,
      {
        line,
        lineNumber: index + 1,
      }
    ]
  }, []);

  return lines;
}

const analyzeSentences = (lines) => {
  return lines.reduce((result, { line, lineNumber }) => {
    const sentences = line.match(/[^\.!\?]+[\.!\?]+/g);

    // If there are no sentences in the line,
    // Ignore this line to check
    if (sentences === null) {
      return result;
    }

    return [
      ...result,
      {
        lineNumber,
        sentences,
      }
    ];
  }, []);
};

const analyzeWords = (lines, output, markProblem) => {
  const wrongSentences = [];
  let maxLineLength = -Infinity;
  let maxColLength = -Infinity;

  lines.map(({ lineNumber, sentences }) => {
    let previousSentenceOffset = 0;
    sentences.map((sentence) => {
      let hasProblems = false;
      let lastOffset = 0;
      let wordOccurences = [];

      const words = sentence.trim().split(' ');
      const newSentence = words.map((word, wordNumber) => {
        const finalWord = word.toLowerCase().replace(new RegExp(",.!?;", 'g'), '').trim();

        if (NOT_ALLOWED_WORDS.includes(finalWord)) {
          // Mark invalid word
          hasProblems = true;
          markProblem();
          lastOffset = sentence.toLowerCase().indexOf(finalWord, lastOffset) + 1;
          wordOccurences.push(lastOffset);

          return chalk.yellow(word);
        } else {
          return word;
        }
      }).join(' ');
      
      if (hasProblems) {
        const line = lineNumber;
        const col = wordOccurences.map((wordIndex) => previousSentenceOffset + wordIndex).join(', ');

        maxLineLength = Math.max(`${line}`.length, maxLineLength);
        maxColLength = Math.max(`${col}`.length, maxColLength);

        wrongSentences.push({
          line,
          col,
          sentence: newSentence.trim()
        });
      }

      previousSentenceOffset += sentence.length;
    });
  });

  if (wrongSentences.length > 0) {
    output('- The following words are not allowed:\n');
    output(wrongSentences.map(({ line, col, sentence }) => {
      return `${chalk.bold.grey(`  - (Line: ${leftPad(line, maxLineLength)}, Col: ${leftPad(col, maxColLength)}):`)} ${sentence}`;
    }).join('\n'))
  }
};

const analyze = (file, contents) => {
  let hasProblems = false;
  let output = [];
  let problems = 0;

  const analyzers = [
    analyzeLines,
    analyzeSentences,
    analyzeWords,
  ];

  const addOutput = (...args) => output.push(args.join(' '));
  const markProblem = () => {
    hasProblems = true;
    problems += 1;
  };

  return analyzers.reduce((chain, analyzer) => {
    return chain
      .then((input) => analyzer(input, addOutput, markProblem))
      .catch(l); // Log uncaught errors
  }, Promise.resolve(contents))
  .then(() => hasProblems
    ? Promise.reject({ problems, output })
    : Promise.resolve({ problems, output })
  );
};

const formatOutput = (title) => ({ output = [], problems }) => {
  const formattedOutput = output.join('\n');

  if (output.length <= 0) {
    return { output: title, problems };
  }

  return {
    output: `\n${title}\n\n${formattedOutput}\n`,
    problems,
  };
}

// Define program
const program = () => {
  const files = glob.readdirSync('**/*.tex').filter(file => !FILES_TO_IGNORE.includes(file));

  let totalProblems = 0;

  const chain = files.reduce((chain, file) => {
    // Ignore some files
    if (FILES_TO_IGNORE.includes(file)) {
      return chain;
    }

    return chain
      // Start reading the file
      .then(() => readFile(file, 'utf8'))

      // Analyze the contents
      .then((contents) => analyze(file, contents))

      // Notify the user if the analasys was good/bad
      // Also add all the output
      .then(
        formatOutput(chalk.green(`✔ Checking: ./${file}`)),
        formatOutput(chalk.bold.red(`✗ Checking: ./${file}`))
      )
      .then(({ problems, output }) => {
        l(output);
        totalProblems += problems;
        return totalProblems;
      });
  }, Promise.resolve());

  // Finish
  chain.then((problems) => {
    const hasProblems = problems > 0;

    const output = problems === 1
      ? '1 problem occured.' 
      : `${problems} problems occured.`

    l(hasProblems
      ? chalk.bold.red(output)
      : chalk.green(output));
  });
};

// Start
program();
