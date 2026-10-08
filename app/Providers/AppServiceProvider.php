<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Task;
use App\Observers\TaskObserver;
use Illuminate\Console\Scheduling\Schedule;
use App\Jobs\CheckOverdueTasksJob;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(Schedule $schedule): void
    {
        Task::observe(TaskObserver::class);
        $schedule->job(new CheckOverdueTasksJob)->everyMinute();
    }
}
