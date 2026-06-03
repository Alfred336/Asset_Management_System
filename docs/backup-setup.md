# Database Backup Automation Setup

This document explains how to set up automatic daily database backups at midnight with email notifications.

## What's Included

- **Backup Command**: `app/Console/Commands/BackupDatabaseCommand.php` - Executes daily backups
- **Notification**: `app/Notifications/BackupCompletedNotification.php` - Sends email when backup completes
- **Scheduler**: `app/Console/Kernel.php` - Defines the backup schedule (midnight daily)
- **Cron Entry**: `cron/laravel-scheduler` - System cron job to trigger the scheduler

## Setup Instructions

### Step 1: Make the setup script executable
```bash
chmod +x scripts/setup-backup-scheduler.sh
```

### Step 2: Run the setup script (requires sudo)
```bash
sudo ./scripts/setup-backup-scheduler.sh
```

This will:
- Install a cron job at `/etc/cron.d/laravel-scheduler`
- Run `php artisan schedule:run` every minute
- Trigger your backup at midnight (00:00) automatically

### Step 3: Verify Mail Configuration

Ensure your `.env` file has proper mail settings:
```env
MAIL_DRIVER=smtp
MAIL_HOST=your-mail-server
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Asset Management System"
```

## How It Works

1. **Daily Trigger**: Cron runs `php artisan schedule:run` every minute
2. **Scheduled Task**: Laravel Scheduler checks if tasks need to run
3. **Midnight Execution**: At 00:00, the `backup:database` command is triggered
4. **Backup Process**: 
   - Executes `scripts/backup-db.sh`
   - Creates compressed backup in `storage/backups/database/`
5. **Notification**: 
   - Finds all users with `role = 'admin'`
   - Sends email notification to each admin
   - Includes backup file location and timestamp

## Manual Testing

To test the backup and notification manually:

```bash
# Run the backup command directly
php artisan backup:database

# This will:
# - Create a backup file
# - Send notifications to all admin users
# - Display success/error messages
```

## Monitoring

### Check if cron job is running
```bash
sudo service cron status
```

### View cron execution logs
```bash
sudo grep CRON /var/log/syslog | tail -20
# or on some systems:
sudo journalctl -u cron | tail -20
```

### View Laravel schedule runs
```bash
# Check storage/logs/laravel.log for scheduled task execution
tail -f storage/logs/laravel.log | grep "schedule"
```

### List existing backups
```bash
ls -lah storage/backups/database/
```

## Configuration Options

### Change Backup Time
Edit `app/Console/Kernel.php`:
```php
$schedule->command('backup:database')
    ->dailyAt('02:00')  // Change from '00:00' to desired time
    ->runInBackground()
    ->withoutOverlapping();
```

### Notify All Users (not just admins)
Edit `app/Console/Commands/BackupDatabaseCommand.php` in the `notifyAdmins()` method:
```php
// Uncomment this line to notify all users:
// User::all()->each(fn($user) => $user->notify(new BackupCompletedNotification($backupFile, $output)));
```

### Change Backup Retention
Edit `scripts/backup-db.sh` to modify how many backups are kept:
```bash
# Default keeps last 7 backups, look for RETAIN_COUNT in the script
```

## Disable Backups

To temporarily or permanently disable backups:

### Option 1: Disable via cron
```bash
sudo rm /etc/cron.d/laravel-scheduler
```

### Option 2: Disable via user crontab
```bash
crontab -e
# Comment out or delete the laravel scheduler line
```

### Option 3: Disable in code
Comment out the schedule in `app/Console/Kernel.php`:
```php
// $schedule->command('backup:database')
//     ->dailyAt('00:00')
//     ->runInBackground()
//     ->withoutOverlapping();
```

## Troubleshooting

### Backups not running
1. Check if cron service is running: `sudo service cron status`
2. Check cron logs: `sudo grep CRON /var/log/syslog`
3. Verify cron file exists: `ls /etc/cron.d/laravel-scheduler`
4. Test manually: `php artisan backup:database`

### Email notifications not sending
1. Check mail configuration in `.env`
2. Verify SMTP credentials are correct
3. Test mail: `php artisan tinker` then `Mail::raw('test', fn($m) => $m->to('test@example.com'));`
4. Check `storage/logs/laravel.log` for mail errors

### Permission errors
1. Ensure web server user can write to `storage/backups/database/`
2. Run: `chmod -R 755 storage/backups/`
3. Run: `chown -R www-data:www-data storage/` (adjust user to your web server)

## Additional Features

The backup script supports multiple database types:
- **SQLite** (default)
- **MySQL** (configurable via `.env`)
- **PostgreSQL** (configurable via `.env`)

All database credentials are read from your `.env` file automatically.

## Files Reference

- `app/Console/Kernel.php` - Task scheduler configuration
- `app/Console/Commands/BackupDatabaseCommand.php` - Backup execution logic
- `app/Notifications/BackupCompletedNotification.php` - Email template
- `scripts/backup-db.sh` - Database backup script
- `scripts/setup-backup-scheduler.sh` - Installation script
- `cron/laravel-scheduler` - Cron job configuration
