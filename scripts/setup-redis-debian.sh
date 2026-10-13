#!/usr/bin/env bash
# Ensures Redis is enabled for local Laravel use on Debian (host / hypervisor).
# Run once: sudo bash scripts/setup-redis-debian.sh

set -euo pipefail

if [[ "${EUID:-$(id -u)}" -ne 0 ]]; then
  echo "Run as root: sudo bash $0" >&2
  exit 1
fi

if ! command -v redis-server >/dev/null 2>&1; then
  echo "Installing redis-server..."
  apt-get update -qq
  apt-get install -y redis-server
fi

CONF="/etc/redis/redis.conf"
if [[ -f "${CONF}" ]]; then
  # Local-only binding is appropriate when Laravel runs on the same host.
  sed -i 's/^supervised no/supervised systemd/' "${CONF}" 2>/dev/null || true
  if grep -q '^bind ' "${CONF}"; then
    sed -i 's/^bind .*/bind 127.0.0.1 ::1/' "${CONF}"
  fi
fi

systemctl enable redis-server
systemctl restart redis-server

if redis-cli ping | grep -q PONG; then
  echo "Redis is running on 127.0.0.1:6379"
else
  echo "Redis failed health check" >&2
  exit 1
fi
