<?php

namespace App\Observers;

use App\Models\Report;
use App\Notifications\ReportArchivedNotification;
use App\Notifications\ReportRestoredNotification;
class ReportObserver
{
  /**
    * Handle the Report "created" event.
  */
  public function created(Report $report): void
  {
    //
  }

  /**
    * Handle the Report "updated" event.
  */
  public function updated(Report $report): void
  {
    //
  }

  /**
    * Handle the Report "deleted" event.
  */
  public function deleted(Report $report): void
  {
    if ($report->isForceDeleting() === false && $report->user) {
      $report->user->notify(new ReportArchivedNotification($report));
    }
  }

  /**
    * Handle the Report "restored" event.
  */
  public function restored(Report $report): void
  {
    if($report->user){
      $report->user->notify(new ReportRestoredNotification($report));
    }
  }

  /**
    * Handle the Report "force deleted" event.
  */
  public function forceDeleted(Report $report): void
  {
    //
  }
}
