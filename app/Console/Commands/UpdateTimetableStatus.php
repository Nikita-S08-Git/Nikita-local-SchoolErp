<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateTimetableStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'timetable:update-status {--dry-run : Show what would be updated without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update timetable status based on date (closed for past dates, active for today, upcoming for future)';

    /**
     * Execute the console command.
     * 
     * Status Logic:
     * - Past date (yesterday or older): 'closed'
     * - Today: 'active'
     * - Future: 'upcoming'
     */
    public function handle()
    {
        $this->info('Updating timetable statuses based on dates...');
        
        $yesterday = now()->subDay()->format('Y-m-d');
        $today = now()->format('Y-m-d');
        
        if ($this->option('dry-run')) {
            $this->info('DRY RUN MODE - No changes will be made');
        }
        
        // Get timetables with past dates that should be closed
        $pastTimetables = DB::table('timetables')
            ->whereNotNull('date')
            ->where('date', '<=', $yesterday)
            ->whereNotIn('status', ['closed', 'cancelled', 'completed'])
            ->count();
            
        $this->info("Timetables with past dates (should be closed): {$pastTimetables}");
        
        // Get timetables with today's date that should be active
        $todayTimetables = DB::table('timetables')
            ->whereNotNull('date')
            ->where('date', $today)
            ->whereNotIn('status', ['active', 'cancelled', 'completed'])
            ->count();
            
        $this->info("Timetables with today's date (should be active): {$todayTimetables}");
        
        // Get timetables with future dates that should be upcoming
        $futureTimetables = DB::table('timetables')
            ->whereNotNull('date')
            ->where('date', '>', $today)
            ->whereNotIn('status', ['upcoming', 'cancelled', 'completed'])
            ->count();
            
        $this->info("Timetables with future dates (should be upcoming): {$futureTimetables}");
        
        if (!$this->option('dry-run')) {
            // Update past dates to closed
            $closedCount = DB::table('timetables')
                ->whereNotNull('date')
                ->where('date', '<=', $yesterday)
                ->whereNotIn('status', ['closed', 'cancelled', 'completed'])
                ->update(['status' => 'closed']);
                
            $this->info("Updated {$closedCount} timetables to 'closed' status.");
            
            // Update today's date to active
            $activeCount = DB::table('timetables')
                ->whereNotNull('date')
                ->where('date', $today)
                ->whereNotIn('status', ['active', 'cancelled', 'completed'])
                ->update(['status' => 'active']);
                
            $this->info("Updated {$activeCount} timetables to 'active' status.");
            
            // Update future dates to upcoming
            $upcomingCount = DB::table('timetables')
                ->whereNotNull('date')
                ->where('date', '>', $today)
                ->whereNotIn('status', ['upcoming', 'cancelled', 'completed'])
                ->update(['status' => 'upcoming']);
                
            $this->info("Updated {$upcomingCount} timetables to 'upcoming' status.");
            
            $this->info('Timetable status update completed successfully!');
        } else {
            $this->info('DRY RUN COMPLETE - No changes were made.');
            $this->info('Run without --dry-run to apply changes.');
        }
        
        return Command::SUCCESS;
    }
}
