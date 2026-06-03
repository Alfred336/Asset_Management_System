#!/usr/bin/env bash
# Setup automatic database backups with Laravel scheduler
# This script configures a system cron job to run Laravel's schedule:run command every minute

set -euo pipefail

PROJECT_PATH="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
CRON_JOB="* * * * * cd $PROJECT_PATH && php artisan schedule:run >> /dev/null 2>&1"

echo "=========================================="
echo "Setting up daily database backups"
echo "=========================================="
echo ""
echo "Project Path: $PROJECT_PATH"
echo ""

# Check if running as root for cron setup
if [[ $EUID -eq 0 ]]; then
    echo "Setting up system-wide cron job..."
    
    # Create cron file in /etc/cron.d/
    echo "$CRON_JOB" | sudo tee /etc/cron.d/laravel-scheduler > /dev/null
    sudo chmod 644 /etc/cron.d/laravel-scheduler
    
    echo "✅ Cron job installed to /etc/cron.d/laravel-scheduler"
else
    echo "Adding to user's crontab (run 'sudo crontab -e' if you prefer system-wide installation)..."
    
    # Add to user crontab
    (crontab -l 2>/dev/null | grep -v "laravel scheduler" || true; echo "$CRON_JOB") | crontab -
    
    echo "✅ Cron job added to user crontab"
fi

echo ""
echo "=========================================="
echo "Configuration complete!"
echo "=========================================="
echo ""
echo "Backup Schedule:"
echo "  • Time: Every day at 00:00 (midnight)"
echo "  • Location: storage/backups/database/"
echo "  • Notifications: Email to all admin users"
echo ""
echo "To verify the setup:"
echo "  1. Check cron status: sudo service cron status"
echo "  2. Check cron logs: sudo grep CRON /var/log/syslog | tail -20"
echo "  3. Run backup manually: php artisan backup:database"
echo ""
echo "To disable backups, edit your crontab or remove /etc/cron.d/laravel-scheduler"
