#!/bin/bash
cd /var/www/html/Projet-CIEL-IOT

git pull origin main --rebase
git add .

if [[ $(git status --porcelain) ]]; then
  git commit -m "Auto commit - $(date '+%Y-%m-%d %H:%M:%S')"
  git push origin main
fi
