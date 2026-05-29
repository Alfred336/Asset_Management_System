import { exec } from 'node:child_process';
import path from 'node:path';
import cron from 'node-cron';

const ROOT = path.resolve(path.dirname(new URL(import.meta.url).pathname), '..');
const BACKUP_SCRIPT = path.join(ROOT, 'scripts', 'backup-db.sh');

function runBackup() {
  console.log(new Date().toISOString(), 'Starting scheduled backup...');
  const cmd = `/usr/bin/env bash "${BACKUP_SCRIPT}"`;
  const child = exec(cmd, { cwd: ROOT, env: process.env });
  child.stdout.pipe(process.stdout);
  child.stderr.pipe(process.stderr);
  child.on('exit', (code) => {
    console.log(new Date().toISOString(), `Backup process exited with code ${code}`);
  });
}

// Schedule daily at 02:00
const schedule = '0 2 * * *';
cron.schedule(schedule, () => {
  runBackup();
}, {
  scheduled: true,
  timezone: Intl.DateTimeFormat().resolvedOptions().timeZone || 'UTC'
});

console.log('Backup scheduler started — running daily at 02:00 (local time).');

// Keep process alive
process.stdin.resume();
