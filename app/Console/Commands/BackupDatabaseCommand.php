<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Notifications\BackupCompletedNotification;
use App\Models\User;
use Symfony\Component\Process\Process;

class BackupDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Backup the application database and notify administrators';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            $this->info('Starting database backup...');

            // Run the backup script
            $backupScript = base_path('scripts/backup-db.sh');
            $process = new Process(['bash', $backupScript]);
            $process->setTimeout(300); // 5 minutes timeout
            $process->run();

            if (!$process->isSuccessful()) {
                $this->error('Backup failed: ' . $process->getErrorOutput());
                return self::FAILURE;
            }

            $output = $process->getOutput();
            $this->info($output);

            // Get backup file path from output
            preg_match('/Backup saved to (.+)/', $output, $matches);
            $backupFile = $matches[1] ?? null;

            // Send notifications to admin users
            $this->notifyAdmins($backupFile, $output);

            $this->info('Database backup completed successfully!');
            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Backup error: ' . $e->getMessage());
            return self::FAILURE;
        }
    }

    /**
     * Send notification to all users that have a role
     */
    private function notifyAdmins(?string $backupFile, string $output): void
    {
        try {
            // Notify all users that have a role assigned
            $users = User::whereHas('roles')->get();
            
            if ($users->isEmpty()) {
                $this->warn('No users with roles found.');
                return;
            }
            
            $users->each(fn($user) => $user->notify(new BackupCompletedNotification($backupFile, $output)));
            
            $this->info('Notifications sent to ' . $users->count() . ' user(s) with role(s).');
        } catch (\Exception $e) {
            $this->warn('Failed to send notifications: ' . $e->getMessage());
        }
    }
}
