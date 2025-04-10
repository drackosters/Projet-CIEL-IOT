#!/bin/bash
cd /var/www/html/Projet-CIEL-IOT || exit

# Nouveau système de logging
LOG_FILE="/var/log/git-sync.log"
exec > >(tee -a "$LOG_FILE") 2>&1

# Authentification SSH
export GIT_SSH_COMMAND="ssh -i ~/.ssh/id_ed25519 -o IdentitiesOnly=yes"

# Synchronisation sécurisée
{
  git stash push -u -m "temp_$(date +%s)"
  git pull origin main --rebase
  git stash pop
  git add . ":!sync.log"  # Exclut explicitement sync.log
  git commit -m "Sync: $(date '+%Y-%m-%d %H:%M:%S')" 
  git push origin main
} || {
  echo "Erreur détectée - Réinitialisation"
  git reset --hard HEAD
  git clean -fd
}
