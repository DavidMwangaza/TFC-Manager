#!/usr/bin/env bash
set -euo pipefail

# Copie .env si manquant
if [ ! -f .env ]; then
  cp .env.example .env
  echo "Fichier .env créé depuis .env.example"
fi

# Générer APP_KEY
php artisan key:generate --ansi

# Migrer et seed
php artisan migrate:fresh --seed --force

# Lier le storage (ignore l'erreur si déjà lié)
php artisan storage:link || true

# Installer dépendances JS
npm install --no-audit --no-fund

# Démarrer serveur Laravel + Vite en parallèle
if command -v npx >/dev/null 2>&1; then
  npx concurrently -c "#93c5fd,#c4b5fd" "php artisan serve --host=127.0.0.1 --port=8000" "npm run dev" --names=server,vite --kill-others
else
  echo "npx non trouvé : Démarrage du serveur Laravel seul"
  php artisan serve --host=127.0.0.1 --port=8000
fi
