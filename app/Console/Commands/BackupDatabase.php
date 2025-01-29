<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Spatie\Backup\Commands\BackupCommand;

class BackupDatabase extends Command
{
    protected $signature = 'backup:run-custom';
    protected $description = 'Run a custom backup';

    public function handle()
    {
        $this->info('Starting backup...');
        // $this->call('backup:run'); // Call the spatie backup command
        Artisan::call('backup:run --only-db');


        $this->info('Backup completed!');
    }
}