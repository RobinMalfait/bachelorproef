#!/bin/bash

rm -rf `biber --cache`

npm run cleanup

pdflatex bachproef-tin.tex
pdflatex bachproef-tin.tex
makeglossaries bachproef-tin
pdflatex bachproef-tin.tex
biber bachproef-tin
pdflatex bachproef-tin.tex
pdflatex bachproef-tin.tex

npm run cleanup

clear

echo "============================"
echo "======== I AM DONE  ========"
echo "============================"
