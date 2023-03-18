<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Optimization;
use Carbon\Carbon;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->job(function () {
            $out = new \Symfony\Component\Console\Output\ConsoleOutput();
            $trades = Optimization::where('status', 'Paid')->where('isApproved', false)->get();
            foreach($trades as $t){
                
                $break_1_start = Carbon::parse($t->created_at);
                $break_1_ends = Carbon::now();
                $diff_in_hours = $break_1_ends->diffInDays($break_1_start);
                if($diff_in_hours > 1){
                    $t->update([
                        'isApproved' => true
                    ]);
                }
                
            }

        })->daily();
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
