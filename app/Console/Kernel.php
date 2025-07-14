<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\UpdateVerifikasiWisudaStatus::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Run every minute for testing - change to hourly() for production
        $schedule->command('wisuda:update-status')
                 ->everyMinute()
                 ->withoutOverlapping()
                 ->appendOutputTo(storage_path('logs/wisuda-scheduler.log'))
                 ->before(function() {
                     \Log::info('Wisuda scheduler: Starting command execution at ' . now());
                 })
                 ->after(function() {
                     \Log::info('Wisuda scheduler: Command execution completed at ' . now());
                 })
                 ->onFailure(function() {
                     \Log::error('Wisuda scheduler: Command failed at ' . now());
                 });
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}