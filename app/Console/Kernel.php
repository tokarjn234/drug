<?php

namespace App\Console;
require_once app_path('Lib/Functions.php');
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\Inspire::class,
        \App\Console\Commands\Address::class,
        \App\Console\Commands\OpenSSLCert::class,
        \App\Console\Commands\OpenSSLCertCompany::class,
        \App\Console\Commands\OpenSSLCertMediaid::class,
        \App\Console\Commands\UserCmd::class,
        \App\Console\Commands\ApiTool::class,
        \App\Console\Commands\SendMailReminders::class,
        \App\Console\Commands\TimeDeleteImage::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('inspire')
            ->hourly();
    }
}
