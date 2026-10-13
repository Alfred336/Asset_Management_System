#!/usr/bin/env bash
# Install nginx vhost for Asset Management System on Debian.
# Run: sudo bash scripts/install-nginx-site-debian.sh

set -euo pipefail

PROJECT_ROOT="$(cd "$(dirname "$0")/.." && pwd)"
SITE_NAME="asset-management"
AVAILABLE="/etc/nginx/sites-available/${SITE_NAME}"
ENABLED="/etc/nginx/sites-enabled/${SITE_NAME}"

if [[ "${EUID:-$(id -u)}" -ne 0 ]]; then
  echo "Run as root: sudo bash $0" >&2
  exit 1
fi

sed "s|/home/kernel/Asset_Management_System|${PROJECT_ROOT}|g" \
  "${PROJECT_ROOT}/scripts/nginx-asset-management.conf" > "${AVAILABLE}"

ln -sf "${AVAILABLE}" "${ENABLED}"
rm -f /etc/nginx/sites-enabled/default

nginx -t
systemctl reload nginx

echo "Site enabled. APP_URL should match: http://$(hostname -I | awk '{print $1}')"
