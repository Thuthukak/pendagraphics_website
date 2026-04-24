<?php

use Illuminate\Foundation\Console\ClosureCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    /** @var ClosureCommand $this */
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('sitemap:generate')->weekly();

    // Recurring invoice generator — once daily
Schedule::command('invoices:process-recurring')
        ->dailyAt('06:00') // for testing set to everyMinute() else dailyAt('06:00')
        ->timezone('Africa/Johannesburg')
        ->withoutOverlapping()
        ->runInBackground()
        ->emailOutputOnFailure(config('mail.from.address'));

// Retry any failed invitation jobs every 5 minutes
Schedule::command('queue:retry all')
    ->everyFiveMinutes()
    ->name('retry-failed-invitation-jobs')
    ->withoutOverlapping();

//    Queue worker — runs every minute, stops when queue is empty
//    withoutOverlapping() is the critical guard: if a previous worker
//    is still chewing through a big batch, don't spawn another one.
Schedule::command('queue:work --stop-when-empty --timeout=60')
        ->everyMinute()
        ->withoutOverlapping(5)   // 5-min lock TTL
        ->runInBackground();

// Keep the failed_jobs table clean — prune old failures after 7 days
Schedule::command('queue:flush')
    ->weekly()
    ->name('flush-old-failed-jobs');
