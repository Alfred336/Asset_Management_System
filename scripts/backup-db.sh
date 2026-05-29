#!/usr/bin/env bash
set -euo pipefail

# Daily database backup script for multiple DB drivers (sqlite/mysql/pgsql)
# - Reads basic DB settings from the project's .env file when present
# - Saves backups to storage/backups/database
# - Keeps last 7 backups by default

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
ENV_FILE="$ROOT/.env"

get_env() {
  local key="$1"
  if [ -f "$ENV_FILE" ]; then
    grep -m1 -E "^${key}=" "$ENV_FILE" | sed -E "s/^${key}=(.*)$/\1/" | sed 's/^"//' | sed 's/"$//'
  fi
}

DB_CONNECTION="$(get_env DB_CONNECTION || true)"
if [ -z "$DB_CONNECTION" ]; then
  DB_CONNECTION=sqlite
fi

DB_DATABASE="$(get_env DB_DATABASE || true)"
DB_HOST="$(get_env DB_HOST || true)"
DB_PORT="$(get_env DB_PORT || true)"
DB_USERNAME="$(get_env DB_USERNAME || true)"
DB_PASSWORD="$(get_env DB_PASSWORD || true)"

BACKUP_DIR="$ROOT/storage/backups/database"
mkdir -p "$BACKUP_DIR"
TS="$(date +%Y%m%d-%H%M%S)"
OUT_BASE="$BACKUP_DIR/db-$TS"

case "$DB_CONNECTION" in
  sqlite)
    if [ -z "$DB_DATABASE" ]; then
      DB_DATABASE="$ROOT/database/database.sqlite"
    fi
    if [[ "$DB_DATABASE" != /* ]]; then
      DB_DATABASE="$ROOT/$DB_DATABASE"
    fi
    if [ ! -f "$DB_DATABASE" ]; then
      echo "SQLite DB not found: $DB_DATABASE" >&2
      exit 1
    fi
    cp "$DB_DATABASE" "$OUT_BASE.sqlite"
    gzip -f "$OUT_BASE.sqlite"
    OUT_FILE="$OUT_BASE.sqlite.gz"
    ;;

  mysql|mariadb)
    OUT_FILE="$OUT_BASE.sql.gz"
    export MYSQL_PWD="$DB_PASSWORD"
    mysqldump --single-transaction --quick --routines --events -h "${DB_HOST:-127.0.0.1}" -P "${DB_PORT:-3306}" -u "${DB_USERNAME:-root}" "${DB_DATABASE}" | gzip > "$OUT_FILE"
    unset MYSQL_PWD
    ;;

  pgsql|postgres|postgresql)
    OUT_FILE="$OUT_BASE.sql.gz"
    export PGPASSWORD="$DB_PASSWORD"
    pg_dump -h "${DB_HOST:-127.0.0.1}" -p "${DB_PORT:-5432}" -U "${DB_USERNAME:-root}" "${DB_DATABASE}" | gzip > "$OUT_FILE"
    unset PGPASSWORD
    ;;

  *)
    echo "Unsupported DB_CONNECTION: $DB_CONNECTION" >&2
    exit 1
    ;;
esac

echo "Backup saved to $OUT_FILE"


# Optional: upload to S3 if `AWS_S3_BUCKET` is set in .env or environment
AWS_S3_BUCKET="$(get_env AWS_S3_BUCKET || true)"
AWS_S3_PREFIX="$(get_env AWS_S3_PREFIX || true)"
AWS_REGION="$(get_env AWS_REGION || true)"
AWS_PROFILE="$(get_env AWS_PROFILE || true)"

if [ -n "$AWS_S3_BUCKET" ]; then
  if command -v aws >/dev/null 2>&1; then
    echo "Uploading $OUT_FILE to S3 bucket $AWS_S3_BUCKET..."
    DEST="s3://${AWS_S3_BUCKET}"
    if [ -n "$AWS_S3_PREFIX" ]; then
      DEST="${DEST%/}/$AWS_S3_PREFIX"
    fi
    DEST="${DEST%/}/$(basename "$OUT_FILE")"

    PROFILE_ARG=""
    if [ -n "$AWS_PROFILE" ]; then
      PROFILE_ARG="--profile $AWS_PROFILE"
    fi
    REGION_ARG=""
    if [ -n "$AWS_REGION" ]; then
      REGION_ARG="--region $AWS_REGION"
    fi

    # run upload but don't let a failing upload abort rotation or the script
    set +e
    aws s3 cp "$OUT_FILE" "$DEST" $REGION_ARG $PROFILE_ARG
    rc=$?
    set -e

    if [ $rc -ne 0 ]; then
      echo "S3 upload failed (aws exit code $rc)" >&2
    else
      echo "S3 upload succeeded: $DEST"
    fi
  else
    echo "aws CLI not found; skipping S3 upload" >&2
  fi
fi

# Rotation: keep last N backups (files) in the backup directory
KEEP=7
cd "$BACKUP_DIR"
ls -1t | tail -n +$((KEEP+1)) | xargs -r -I {} rm -- "{}"

exit 0
