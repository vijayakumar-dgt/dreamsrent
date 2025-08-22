<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\GeneralSetting\Models\Dbbackup;
use ZipArchive;

class CustomSystemBackup extends Command
{
    protected $signature = 'backup:system';
    protected $description = 'Backup the entire system files as a zip archive';

    public function handle(): int
    {
        $backupDir = storage_path('app/public/backups');

        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0777, true);
        }

        $fileName = 'system_backup_' . now()->format('Y_m_d_His') . '.zip';
        $backupPath = $backupDir . '/' . $fileName;

        $rootDir = base_path();

        try {
            $zip = new ZipArchive();
            if ($zip->open($backupPath, ZipArchive::CREATE) === true) {
                $this->zipDirectory($rootDir, $zip, $rootDir);
                $zip->close();

                // Store backup record in the database
                Dbbackup::create([
                    'name'       => $fileName,
                    'type'       => '2',
                    'created_at' => now(),
                ]);

                $this->info("System backup stored as: $fileName and recorded in Dbbackup table.");
            } else {
                throw new \Exception("Could not create the zip archive.");
            }
        } catch (\Exception $e) {
            $this->error("System backup failed: " . $e->getMessage());
        }
        return 0;
    }

    private function zipDirectory(string $folder, ZipArchive $zip, string $root): bool
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($folder, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($files as $file) {
            $localPath = str_replace($root . DIRECTORY_SEPARATOR, '', $file->getPathname());
            if ($file->isDir()) {
                $zip->addEmptyDir($localPath);
            } else {
                $zip->addFile($file->getPathname(), $localPath);
            }
        }
        return true;
    }
}
