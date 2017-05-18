#!/bin/bash

rm -rf `biber --cache`

pdflatex bachproef-tin
makeglosseries bachproef-tin
pdflatex bachproef-tin.tex
biber bachproef-tin
pdflatex bachproef-tin.tex
pdflatex bachproef-tin.tex

clear

echo "============================"
echo "======== I AM DONE  ========"
echo "============================"
