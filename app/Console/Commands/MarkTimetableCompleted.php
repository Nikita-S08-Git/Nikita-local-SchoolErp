<?php

namespace App\Console\Commands;

use App\Models\Academic\Timetable;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MarkTimetableCompleted extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'timetable:mark-completed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark timetables as completed when their scheduled time has passed';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting timetable status update...');
        
        $now = Carbon::now();
        $today = $now->format('Y-m-d');
        $currentTime = $now->format('H:i:s');
        
        // For date-specific timetables that have passed
        $dateTimetables = Timetable::where('status', Timetable::STATUS_ACTIVE)
            ->whereNotNull('date')
            ->where('date', '<', $today)
            ->get();
            
        $dateCount = 0;
        foreach ($dateTimetables as $timetable) {
            // Check if end_time has passed
            if ($timetable->end_time && $timetable->end_time < $now->format('H:i:s')) {
                $timetable->update(['status' => Timetable::STATUS_COMPLETED]);
                $dateCount++;
                $subjectName = $timetable->subject->name ?? 'N/A';
                $this->line("Marked completed: Date-specific - {$timetable->date} - {$subjectName}");
            }
        }
        
        // For weekly recurring timetables - check if current day's lecture has ended
        $dayOfWeek = strtolower($now->format('l'));
        
        // Get all active weekly timetables for today that have ended
        $weeklyTimetables = Timetable::where('status', Timetable::STATUS_ACTIVE)
            ->whereNull('date')
            ->where('day_of_week', $dayOfWeek)
            ->where('end_time', '<', $currentTime)
            ->get();
            
        $weeklyCount = 0;
        foreach ($weeklyTimetables as $timetable) {
            $timetable->update(['status' => Timetable::STATUS_COMPLETED]);
            $weeklyCount++;
            $subjectName = $timetable->subject->name ?? 'N/A';
            $this->line("Marked completed: Weekly - {$timetable->day_of_week} - {$subjectName}");
        }
        
        // Also mark future timetables as "upcoming" status
        $upcomingTimetables = Timetable::where('status', Timetable::STATUS_ACTIVE)
            ->where(function($query) use ($today, $dayOfWeek, $currentTime) {
                $query->where(function($q) use ($today, $currentTime) {
                    $q->whereNotNull('date')
                      ->where('date', '>', $today);
                })->orWhere(function($q) use ($dayOfWeek, $currentTime) {
                    $q->whereNull('date')
                      ->where('day_of_week', '!=', $dayOfWeek);
                })->orWhere(function($q) use ($dayOfWeek, $currentTime) {
                    $q->whereNull('date')
                      ->where('day_of_week', $dayOfWeek)
                      ->where('start_time', '>', $currentTime);
                });
            })
            ->get();
            
        // Note: We're not changing status to "upcoming" as that status doesn't exist
        // But we could add it if needed
        
        $this->info("Completed!");
        $this->info("Marked {$dateCount} date-specific timetables as completed.");
        $this->info("Marked {$weeklyCount} weekly timetables as completed.");
        
        return Command::SUCCESS;
    }
}
