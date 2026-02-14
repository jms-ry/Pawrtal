<?php

namespace App\Console\Commands;

use App\Models\InspectionSchedule;
use App\Notifications\DailyInspectionReminderNotification;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class NotifyTodaysInspections extends Command
{
  /**
    * The name and signature of the console command.
  */
  protected $signature = 'inspections:notify-today';

  /**
    * The console command description.
  */
  protected $description = 'Send notifications to inspectors for inspections scheduled today';

  /**
    * Execute the console command.
  */
  public function handle()
  {
    $today = now()->toDateString();

    // Find inspections for today, excluding done/cancelled (but including NULL)
    $todaysInspections = InspectionSchedule::whereDate('inspection_date', $today)
      ->where(function($query) {
        $query->whereNotIn('status', ['done', 'cancelled'])->orWhereNull('status');
      })
      ->with(['user', 'adoptionApplication.user'])
    ->get();

    $notifiedCount = 0;

    foreach ($todaysInspections as $inspection) {
      if ($inspection->inspector_id && $inspection->user) {
        $inspection->user->notify(new DailyInspectionReminderNotification($inspection));
            
        $notifiedCount++;
            
        $this->info("Notified inspector: {$inspection->user->fullName()} for inspection #{$inspection->id}");
      }
    }

    if ($notifiedCount === 0) {
      $this->info('No inspections scheduled for today.');
      Log::info('Inspection notifications: No inspections for today');
    } else {
      $this->info("Sent {$notifiedCount} inspection reminder(s).");
      Log::info("Inspection notifications: Sent {$notifiedCount} reminders for today");
    }

    return 0;
  }
}