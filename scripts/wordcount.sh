#!/bin/bash

echo
echo
echo "Words: $(pdftotext bachproef-tin.pdf - | wc -w)"
echo
echo