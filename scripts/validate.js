// Dependencies
const fs = require('fs');
const chalk = require('chalk');
const leftPad = require('left-pad');
const glob = require('glob-fs')({gitignore: true});
const promisify = require('es6-promisify');

// Setup
const config = require('../package.json').validator;
const FILES_TO_IGNORE = config.ignore_files;
const NOT_ALLOWED_WORDS = config.forbidden_words;
const SKIPPABLE_WORDS = config.skippable_words;
const ALTERNATIVE_WORDS = config.alternatives;
const WARNING_WORDS = config.warnings;

// Promisify functions
const readFile = promisify(fs.readFile);

// Define helper utils
const l = console.log;

const analyzeLines = (contents) => {
  const lines = contents.split('\n').reduce((result, line, index) => {
    // Strip lines which are comments
    if (line.indexOf('%') === 0) {
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

const analyzeWords = (lines, output, markError, markWarning) => {
  const wrongSentences = [];
  let maxLineLength = -Infinity;
  let maxColLength = -Infinity;

  lines.map(({ lineNumber, sentences }) => {
    let previousSentenceOffset = 0;
    sentences.map((sentence) => {
      let hasProblems = false;
      let lastOffset = 0;
      let wordOccurences = [];

      const mark = (finalWord, critical = true) => {
        // Mark invalid word
        hasProblems = true;

        if (critical) {
          markError();
        } else {
          markWarning();
        }

        lastOffset = sentence.toLowerCase().indexOf(finalWord, lastOffset) + 1;
        wordOccurences.push(lastOffset);
      }

      const words = sentence.trim().split(' ');
      const newSentence = words.map((word, wordNumber) => {
        const finalWord = word.toLowerCase().replace(new RegExp(",.!?;", 'g'), '').trim();

        // Mark multiple spaces
        if (finalWord === "") {
          markWarning();
          return chalk.bgRed.white('_');
        }

        if (NOT_ALLOWED_WORDS.includes(finalWord)) {
          mark(finalWord);
          return chalk.bgRed.white(word);
        }

        if (SKIPPABLE_WORDS.includes(finalWord)) {
          mark(finalWord);
          return chalk.underline.gray(word);
        }

        if (Object.keys(ALTERNATIVE_WORDS).includes(finalWord)) {
          mark(finalWord);

          const alternatives = ALTERNATIVE_WORDS[word];
          const alternative = alternatives[Math.floor(Math.random() * alternatives.length)];

          return [
            chalk.red(word),
            chalk.green(alternative)
          ].join('->');
        }

        if (WARNING_WORDS.includes(finalWord)) {
          mark(finalWord, false);

          return chalk.yellow(word);
        }

        return word;
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
    output(wrongSentences.map(({ line, col, sentence }) => {
      return `${chalk.bold.grey(` - (Line: ${leftPad(line, maxLineLength)}, Col: ${leftPad(col, maxColLength)}):`)} ${sentence}`;
    }).join('\n'))
  }
};

const analyze = (file, contents) => {
  let hasProblems = false;
  let output = [];

  let errors = 0;
  let warnings = 0;

  const analyzers = [
    analyzeLines,
    analyzeSentences,
    analyzeWords,
  ];

  const addOutput = (...args) => output.push(args.join(' '));

  const markError = () => {
    hasProblems = true;
    errors += 1;
  };

  const markWarning = () => {
    warnings += 1;
  };

  return analyzers.reduce((chain, analyzer) => {
    return chain
      .then((input) => analyzer(input, addOutput, markError, markWarning))
      .catch(l); // Log uncaught errors
  }, Promise.resolve(contents))
  .then(() => hasProblems
    ? Promise.reject({ errors, warnings, output })
    : Promise.resolve({ errors, warnings, output })
  );
};

const formatOutput = (title) => ({ output = [], errors = 0, warnings = 0 }) => {
  const formattedOutput = output.join('\n');

  if (output.length <= 0) {
    return { output: title, errors, warnings };
  }

  return {
    output: `\n${title}\n\n${formattedOutput}\n`,
    errors,
    warnings
  };
}

// Define program
const program = () => {
  let files = glob.readdirSync('**/*.tex').filter(file => !FILES_TO_IGNORE.includes(file));

  let totalErrors = 0;
  let totalWarnings = 0;

  if (process.argv[2] !== undefined) {
    files = files.filter((file) => `./${file}` === process.argv[2]);
  }

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
      .then(({ errors, warnings, output }) => {
        l(output);

        totalErrors += errors;
        totalWarnings += warnings;

        return {
          totalErrors,
          totalWarnings,
        };
      });
  }, Promise.resolve());

  // Finish
  chain.then(({ totalErrors, totalWarnings }) => {
    const hasErrors = totalErrors > 0;

    const errorOutput = totalErrors === 1
      ? '1 error occured.'
      : `${totalErrors} errors occured.`

    const warningOutput = totalWarnings === 1
      ? '1 warning occured.'
      : `${totalWarnings} warnings occured.`

    const output = `${errorOutput} ${warningOutput}`;

    l(hasErrors
      ? chalk.bold.red(output)
      : chalk.green(output));
  });
};

// Start
program();
