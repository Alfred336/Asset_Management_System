#!/usr/bin/env bash
# One-shot host setup for Asset Management System on Debian (requires sudo).
# Usage: sudo bash scripts/bootstrap-debian-server.sh

set -euo pipefail

PROJECT_ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "${PROJECT_ROOT}"

if [[ "${EUID:-$(id -u)}" -ne 0 ]]; then
  echo "Run as root: sudo bash $0" >&2
  exit 1
fi

bash scripts/setup-postgres-debian.sh
bash scripts/setup-redis-debian.sh
bash scripts/install-nginx-site-debian.sh
bash scripts/install-supervisor-debian.sh

chmod o+x "/home/${SUDO_USER:-kernel}" 2>/dev/null || true

DEPLOY_USER="${SUDO_USER:-kernel}"
chown -R "${DEPLOY_USER}:www-data" storage bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache

sudo -u "${DEPLOY_USER}" npm run build
sudo -u "${DEPLOY_USER}" php artisan storage:link 2>/dev/null || true
sudo -u "${DEPLOY_USER}" php artisan migrate --force
sudo -u "${DEPLOY_USER}" php artisan config:cache
sudo -u "${DEPLOY_USER}" php artisan route:cache
sudo -u "${DEPLOY_USER}" php artisan view:cache

echo "Bootstrap complete. Open APP_URL from .env in a browser."
