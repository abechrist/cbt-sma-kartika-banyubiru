<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class BackupDatabase extends Command
{
    protected $signature = 'app:backup-database
                            {--path= : Custom backup directory path}
                            {--keep=10 : Number of backup files to keep}
                            {--compress : Compress backup file}';

    protected $description = 'Backup SQLite database safely (hot backup when write active)';

    public function handle(): int
    {
        $dbPath = storage_path('app/database.sqlite');
        if (! File::exists($dbPath)) {
            $this->error("Database file not found: {$dbPath}");

            return self::FAILURE;
        }

        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "backup_{$timestamp}.sqlite";

        $backupPath = $this->option('path') ?: storage_path('app/backups');

        if (! File::isDirectory($backupPath)) {
            File::makeDirectory($backupPath, 0755, true);
        }

        $targetFile = $backupPath.'/'.$filename;

        try {
            if ($this->isSqliteHotBackupSafe($dbPath, $targetFile)) {
                $this->info("Backup created successfully: {$targetFile}");
            } else {
                $this->error('Database is currently under write lock. Cannot perform hot backup.');

                return self::FAILURE;
            }

            if ($this->option('compress')) {
                $compressedPath = $targetFile.'.gz';
                $this->compressFile($targetFile, $compressedPath);
                File::delete($targetFile);
                $this->info("Backup compressed: {$compressedPath}");
            }

            $this->cleanupOldBackups($backupPath, (int) $this->option('keep'));

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Backup failed: {$e->getMessage()}");

            return self::FAILURE;
        }
    }

    protected function isSqliteHotBackupSafe(string $dbPath, string $targetFile): bool
    {
        if (! File::exists($dbPath)) {
            $this->error("Database file not found: {$dbPath}");

            return false;
        }

        $writeLockFile = $dbPath.'-journal';

        if (File::exists($writeLockFile)) {
            $journalInfo = File::lastModified($writeLockFile);
            $now = time();

            if (($now - $journalInfo) < 5) {
                return false;
            }
        }

        $fileInfo = new \SplFileInfo($dbPath);
        if ($fileInfo->getSize() === 0) {
            return false;
        }

        File::copy($dbPath, $targetFile);

        return File::exists($targetFile) && File::size($targetFile) > 0;
    }

    protected function compressFile(string $source, string $destination): void
    {
        $gz = gzopen($destination, 'wb');
        if ($gz) {
            $filename = basename($source);
            gzwrite($gz, "Backup: {$filename}\n");
            gzwrite($gz, 'Created: '.now()->toIso8601String()."\n");
            gzwrite($gz, 'Original size: '.File::size($source)." bytes\n");
            gzwrite($gz, "----------------------------------------\n");

            $fh = fopen($source, 'rb');
            while (! feof($fh)) {
                gzwrite($gz, fread($fh, 1024 * 512));
            }
            fclose($fh);
            gzclose($gz);
        }
    }

    protected function cleanupOldBackups(string $backupPath, int $keep): void
    {
        $files = glob($backupPath.'/backup_*.sqlite*');

        if (count($files) <= $keep) {
            return;
        }

        usort($files, function ($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        $filesToDelete = array_slice($files, $keep);

        foreach ($filesToDelete as $file) {
            if (File::isFile($file)) {
                File::delete($file);
            }
        }

        $deletedCount = count($filesToDelete);
        if ($deletedCount > 0) {
            $this->info("Cleaned up {$deletedCount} old backup file(s)");
        }
    }
}
