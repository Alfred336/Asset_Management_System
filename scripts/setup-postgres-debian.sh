#!/usr/bin/env bash
# Creates PostgreSQL role and database for Asset Management System (Debian).
# Run once on the server: sudo bash scripts/setup-postgres-debian.sh

set -euo pipefail

DB_NAME="${DB_NAME:-asset_management}"
DB_USER="${DB_USER:-admin}"
DB_PASSWORD="${DB_PASSWORD:-0948}"

if [[ "${EUID:-$(id -u)}" -ne 0 ]]; then
  echo "Run as root: sudo bash $0" >&2
  exit 1
fi

if ! command -v psql >/dev/null 2>&1; then
  echo "PostgreSQL client not found. Install with: apt install postgresql postgresql-client" >&2
  exit 1
fi

run_psql() {
  sudo -u postgres psql -v ON_ERROR_STOP=1 "$@"
}

echo "Ensuring PostgreSQL is running..."
systemctl enable postgresql >/dev/null 2>&1 || true
systemctl start postgresql

echo "Creating role ${DB_USER} (if missing)..."
run_psql -tAc "SELECT 1 FROM pg_roles WHERE rolname='${DB_USER}'" | grep -q 1 || \
  run_psql -c "CREATE USER ${DB_USER} WITH PASSWORD '${DB_PASSWORD}';"

run_psql -c "ALTER USER ${DB_USER} WITH PASSWORD '${DB_PASSWORD}';"

echo "Creating database ${DB_NAME} (if missing)..."
run_psql -tAc "SELECT 1 FROM pg_database WHERE datname='${DB_NAME}'" | grep -q 1 || \
  run_psql -c "CREATE DATABASE ${DB_NAME} OWNER ${DB_USER} ENCODING 'UTF8' TEMPLATE template0;"

echo "Granting privileges on ${DB_NAME}..."
run_psql -d "${DB_NAME}" -c "GRANT ALL ON SCHEMA public TO ${DB_USER};"
run_psql -d "${DB_NAME}" -c "ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON TABLES TO ${DB_USER};"
run_psql -d "${DB_NAME}" -c "ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON SEQUENCES TO ${DB_USER};"
run_psql -c "GRANT ALL PRIVILEGES ON DATABASE ${DB_NAME} TO ${DB_USER};"

echo "Done. Connection: host=127.0.0.1 db=${DB_NAME} user=${DB_USER}"
