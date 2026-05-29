# Database backup: script and installation

What I added:

- `scripts/backup-db.sh` — portable backup script supporting `sqlite`, `mysql`/`mariadb`, and `pgsql`.
- `cron/asset_backup.example` — example cron lines to run the script daily.

Quick install:

1. Make the script executable:

```bash
chmod +x scripts/backup-db.sh
```

2. Test a manual run:

```bash
/usr/bin/env bash scripts/backup-db.sh
```

3. Install a daily cron job (edit with `crontab -e` and replace /path/to/project):

```cron
0 2 * * * /usr/bin/env bash /path/to/project/scripts/backup-db.sh >> /var/log/asset_backup.log 2>&1
```

Or install as a system cron file (requires root):

```bash
# as root
cp cron/asset_backup.example /etc/cron.d/asset_backup
# edit /etc/cron.d/asset_backup and replace /path/to/project with the repository path
```

Notes:

- The script reads basic DB settings from `.env` (if present) and falls back to sensible defaults.
- Backups are written to `storage/backups/database` and rotated (keeps last 7 files).
- If your app runs inside Docker, prefer running a scheduler container or configuring the host's cron with access to the project folder and credentials.

S3 upload (optional):

- To enable automatic upload of backups to Amazon S3 set the following env vars in your `.env` or export them in the cron environment:

```
AWS_S3_BUCKET=my-backup-bucket
AWS_S3_PREFIX=asset-management/backups  # optional prefix/folder inside the bucket
AWS_REGION=us-east-1                     # optional
AWS_PROFILE=default                      # optional, if using named profile
```

- The script uses the `aws` CLI to perform the upload. Ensure the `aws` CLI is installed and credentials are available (via `~/.aws/credentials`, environment variables, or EC2/ECS IAM role).

- The upload is best-effort: failures won't remove local backups or stop rotation, but the script will log upload failures to your cron log.

- To install the AWS CLI (Linux):

```bash
curl "https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip" -o "awscliv2.zip"
unzip awscliv2.zip
sudo ./aws/install
```


If you want, I can also:
- Add automatic upload of backups to S3 or another remote store.
- Create a Docker cron service configuration for this repository.

Run Vite with scheduler:

- If you want the dev server to also run the daily backup scheduler, use the new npm script:

```bash
npm run dev:with-backup
```

This starts `vite` and a small Node scheduler that runs `scripts/backup-db.sh` daily at 02:00 local time. Make sure you installed dependencies after pulling changes:

```bash
npm install
```
