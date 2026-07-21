<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BackupDatabase extends Command
{
    protected $signature = 'db:backup';
    protected $description = 'Backup database MySQL';

    public function handle()
{
    $dbHost = env('DB_HOST');
    $dbPort = env('DB_PORT', 3306);
    $dbUser = env('DB_USERNAME');
    $dbPass = env('DB_PASSWORD');
    $dbName = env('DB_DATABASE');

    $filename = "$dbName-backup.sql"; // selalu replace file
    $path = storage_path("app/backups");

    if (!File::exists($path)) {
        File::makeDirectory($path, 0755, true);
    }

    $fullPath = "{$path}/{$filename}";
    $command = "mysqldump -h {$dbHost} -P {$dbPort} -u {$dbUser} -p'{$dbPass}' {$dbName} > {$fullPath}";

    $this->info("Running backup command...");
    $result = null;
    $output = null;
    exec($command, $output, $result);

    if ($result === 0) {
        $this->info("Database backup successful: {$filename}");
    } else {
        $this->error("Backup failed with code {$result}");
    }
}

} 