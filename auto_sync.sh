#!/bin/bash
export SSH_AUTH_SOCK=$HOME/.ssh/ssh_auth_sock

cd /var/www/html/Projet-CIEL-IOT || exit

echo "[`date`] Début de synchronisation" >> sync.log

# On stash les modifs (même les fichiers non suivis)
git stash push -u -m "temp-sync" >> sync.log 2>&1

# On récupère depuis GitHub
git pull origin main --rebase >> sync.log 2>&1

# On remet les fichiers en place
git stash pop >> sync.log 2>&1

# On ajoute tout
git add . >> sync.log 2>&1

# S'il y a des modifications, on commit
if [[ $(git status --porcelain) ]]; then
  echo "[`date`] Changements détectés. Commit en cours." >> sync.log
  git commit -m "Auto commit - $(date '+%Y-%m-%d %H:%M:%S')" >> sync.log 2>&1
  git push origin main >> sync.log 2>&1
else
  echo "[`date`] Aucun changement à commit." >> sync.log
fi
