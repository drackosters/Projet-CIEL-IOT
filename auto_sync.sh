#!/bin/bash
cd /var/www/html/Projet-CIEL-IOT || exit

# Synchronisation bidirectionnelle
git pull origin main
git add .
git commit -m "Auto-sync $(date '+%Y-%m-%d %H:%M:%S')" 2>/dev/null
git push origin main
