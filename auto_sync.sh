#!/bin/bash
cd /var/www/html/Projet-CIEL-IOT || exit

# Authentification SSH
export GIT_SSH_COMMAND="ssh -i ~/.ssh/id_ed25519 -o IdentitiesOnly=yes"

# Synchronisation bidirectionnelle
{
  git stash push -u -m "temp_$(date +%s)"
  git pull origin main --rebase
  git stash pop

  # Ajout et commit de tous les fichiers, y compris sync.log
  git add .
  git commit -m "Auto-sync: $(date '+%Y-%m-%d %H:%M:%S')" 
  git push origin main
} || {
  echo "Erreur détectée - Réinitialisation"
  git reset --hard HEAD
  git clean -fd
}
