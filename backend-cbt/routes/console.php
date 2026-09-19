<?php

use App\Http\Controllers\Examination\StudentExamController;
use App\Models\ExamAttempt;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Schema;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:backup-database 
    {--path= : Custom backup directory path}
    {--keep=10 : Number of backup files to keep}
    {--compress : Compress backup file}', function () {
    $backupPath = $this->option('path') ?: storage_path('app/backups');

    if (! File::isDirectory($backupPath)) {
        File::makeDirectory($backupPath, 0755, true);
    }

    $timestamp = now()->format('Y-m-d_H-i-s');
    $connection = config('database.default');

    if ($connection === 'sqlite') {
        $dbPath = database_path('database.sqlite');

        if (! File::exists($dbPath)) {
            $this->error("Database file not found: {$dbPath}");

            return 1;
        }

        $filename = "backup_{$timestamp}.sqlite";
        $targetFile = $backupPath.'/'.$filename;

        $writeLockFile = $dbPath.'-journal';
        if (File::exists($writeLockFile)) {
            $journalInfo = File::lastModified($writeLockFile);
            $now = time();
            if (($now - $journalInfo) < 5) {
                $this->error('Database is currently under write lock. Cannot perform hot backup.');

                return 1;
            }
        }

        $fileInfo = new SplFileInfo($dbPath);
        if ($fileInfo->getSize() === 0) {
            $this->error('Database file is empty');

            return 1;
        }

        File::copy($dbPath, $targetFile);

        if (! File::exists($targetFile) || File::size($targetFile) === 0) {
            $this->error('Backup file creation failed');

            return 1;
        }
    } else {
        // MySQL/MariaDB: use mysqldump (Hostinger provides it in PATH)
        $filename = "backup_{$timestamp}.sql";
        $targetFile = $backupPath.'/'.$filename;
        $mysqlDump = 'mysqldump';

        // Build mysqldump command from .env config
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        $cmd = escapeshellcmd($mysqlDump).' --host='.escapeshellarg($host).' --port='.escapeshellarg($port)
            .' --user='.escapeshellarg($username).' --single-transaction --routines --triggers';

        if ($password !== '') {
            $cmd .= ' --password='.escapeshellarg($password);
        }

        $cmd .= ' '.escapeshellarg($database).' > '.escapeshellarg($targetFile);

        exec($cmd.' 2>&1', $output, $exitCode);

        if ($exitCode !== 0 || ! File::exists($targetFile) || File::size($targetFile) === 0) {
            $this->error('Backup failed: '.implode("\n", $output));

            return 1;
        }
    }

    $this->info("Backup created successfully: {$targetFile}");

    if ($this->option('compress')) {
        $compressedPath = $targetFile.'.gz';
        $gz = gzopen($compressedPath, 'wb');
        if ($gz) {
            $fh = fopen($targetFile, 'rb');
            while (! feof($fh)) {
                gzwrite($gz, fread($fh, 1024 * 512));
            }
            fclose($fh);
            gzclose($gz);
            File::delete($targetFile);
            $this->info("Backup compressed: {$compressedPath}");
        }
    }

    $keep = (int) $this->option('keep');
    $files = glob($backupPath.'/backup_*');

    if (count($files) > $keep) {
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

    return 0;
})->purpose('Backup database safely (SQLite hot copy or MySQL mysqldump)');

Schedule::call(function () {
    $controller = app(StudentExamController::class);
    $expired = ExamAttempt::query()
        ->where('status', ExamAttempt::STATUS_IN_PROGRESS)
        ->where('ended_at', '<=', now())
        ->get();

    foreach ($expired as $attempt) {
        $controller->autoSubmit($attempt);
    }
})->everyMinute()->name('cbt-auto-submit');

Schedule::call(function () {
    ExamAttempt::query()
        ->where('status', ExamAttempt::STATUS_IN_PROGRESS)
        ->where('ended_at', '<', now()->subMinutes(5))
        ->update(['status' => 'auto_submitted']);
})->everyFiveMinutes()->name('cbt-cleanup-expired-attempts');

Schedule::call(function () {
    \Artisan::call('app:backup-database', ['--keep' => 5]);
})->dailyAt('02:00')->name('cbt-daily-backup');

Schedule::call(function () {
    // SQLite session table has no last_activity_at column; guard against it.
    $table = config('session.table', 'sessions');
    $columns = Schema::getColumnListing($table);

    if (in_array('last_activity_at', $columns, true)) {
        DB::table($table)->where('last_activity_at', '<', now()->subHours(24))->delete();
    } else {
        // Fallback: delete sessions table rows older than 1 day based on last_activity (unix timestamp)
        DB::table($table)->where('last_activity', '<', now()->subDays(1)->timestamp)->delete();
    }

    Cache::flush();
})->dailyAt('03:00')->name('cbt-housekeeping');
