#!/bin/bash
cd /var/www/html/Projet-CIEL-IOT || exit

# Configuration SSH explicite
export GIT_SSH_COMMAND="ssh -i /home/azureuser/.ssh/id_ed25519 -F /dev/null"

# Nettoyage des modifications résiduelles
git reset --hard HEAD
git clean -fd

echo "[$(date)] Début de synchronisation" | tee -a sync.log

# Synchronisation sécurisée
git stash push -u -m "temp_$(date +%s)" 2>&1 | tee -a sync.log
git pull origin main --rebase 2>&1 | tee -a sync.log
git stash pop 2>&1 | tee -a sync.log

# Gestion des conflits
find . -name "*.orig" -delete

# Commit intelligent
if git diff-index --quiet HEAD --; then
  echo "[$(date)] Aucun changement détecté" | tee -a sync.log
else
  git add . 2>&1 | tee -a sync.log
  git commit -m "Auto-sync: $(date '+%Y-%m-%d %H:%M:%S')" 2>&1 | tee -a sync.log
  git push origin main 2>&1 | tee -a sync.log
fi

# Nettoyage final
git gc --auto 2>&1 | tee -a sync.log
