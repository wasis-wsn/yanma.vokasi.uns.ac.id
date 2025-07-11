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
        // Jalankan setiap menit untuk testing - ubah ke hourly() untuk production
        $schedule->command('wisuda:update-status')
                 ->everyMinute()
                 ->withoutOverlapping()
                 ->sendOutputTo(storage_path('logs/wisuda-scheduler.log'))
                 ->emailOutputOnFailure('admin@example.com'); // Optional: add your email
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