<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\GeneralSetting\Models\Dbbackup;
use Spatie\DbDumper\Databases\MySql;

class CustomDatabaseBackup extends Command
{
    protected $signature = 'backup:database';
    protected $description = 'Backup the database as a raw SQL file';

    public function handle(): int
    {
        $backupDir = storage_path('app/public/database');

        // Ensure the backup directory exists
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0777, true);
        }

        // Generate a unique backup filename
        $fileName = 'database_backup_' . now()->format('Y_m_d_His') . '.sql';
        $backupPath = $backupDir . '/' . $fileName;

        // Database credentials
        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');
        $dbHost = config('database.connections.mysql.host');
        $dbPort = config('database.connections.mysql.port', 3306);

        try {
            // Perform the database dump
            MySql::create()
                ->setDbName($dbName)
                ->setUserName($dbUser)
                ->setPassword($dbPass)
                ->setHost($dbHost)
                ->setPort($dbPort)
                ->dumpToFile($backupPath);

            // Store backup record in the database
            Dbbackup::create([
                'name'       => $fileName,
                'created_at' => now(),
            ]);

            $this->info("Database backup stored as: $fileName and recorded in Dbbackup table.");
        } catch (\Exception $e) {
            $this->error("Database backup failed: " . $e->getMessage());
        }
        return 0;
    }
}
