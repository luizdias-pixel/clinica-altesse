#!/bin/sh
# Monta a pasta "dist" que o Netlify publica: só a vitrine (HTML estático) + CSS + imagens.
# Os arquivos PHP NÃO vão para o Netlify, pois ele não executa PHP.
set -e

rm -rf dist
mkdir -p dist/assets

cp vitrine/index.html dist/index.html
cp -r public/assets/css dist/assets/css
cp -r public/assets/img dist/assets/img
