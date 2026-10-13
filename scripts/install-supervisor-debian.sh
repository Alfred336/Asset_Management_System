#!/usr/bin/env bash
# Install Supervisor programs for queue + scheduler.
# Run: sudo bash scripts/install-supervisor-debian.sh

set -euo pipefail

PROJECT_ROOT="$(cd "$(dirname "$0")/.." && pwd)"
DEST="/etc/supervisor/conf.d/asset-management.conf"

if [[ "${EUID:-$(id -u)}" -ne 0 ]]; then
  echo "Run as root: sudo bash $0" >&2
  exit 1
fi

sed "s|/home/kernel/Asset_Management_System|${PROJECT_ROOT}|g" \
  "${PROJECT_ROOT}/scripts/supervisor-asset-management.conf" > "${DEST}"

chmod o+x "$(dirname "${PROJECT_ROOT}")" 2>/dev/null || true

supervisorctl reread
supervisorctl update
supervisorctl status

echo "Supervisor programs installed."
