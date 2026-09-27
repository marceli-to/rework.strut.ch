<?php

use Illuminate\Support\Facades\Schedule;

/*
 * Requires the cron entry from docs/deployment.md:
 * * * * * * cd /path/to/site && php artisan schedule:run >> /dev/null 2>&1
 */

// abandoned uploads (form never saved)
Schedule::command('media:clean-temp')->dailyAt('03:30')->withoutOverlapping();
