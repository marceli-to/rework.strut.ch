<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ClearImageCache extends Command
{
    protected $signature = 'images:clear';
    protected $description = 'Clear the Glide image cache';

    public function handle(): int
    {
        $path = storage_path('app/.glide-cache');

        if (!File::isDirectory($path)) {
            $this->info('Glide cache is already empty.');
            return self::SUCCESS;
        }

        File::deleteDirectory($path);
        $this->info('Glide cache cleared.');

        return self::SUCCESS;
    }
}
