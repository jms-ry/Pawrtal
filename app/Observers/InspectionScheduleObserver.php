<?php

namespace App\Observers;

use App\Models\InspectionSchedule;
use App\Notifications\ApplicantInspectionScheduleNotification;
use App\Notifications\InspectorInspectionScheduleNotification;

class InspectionScheduleObserver
{
  /**
    * Handle the InspectionSchedule "created" event.
   */
  public function created(InspectionSchedule $inspectionSchedule): void
  {
    //notify the applicant
    if($inspectionSchedule->adoptionApplication?->user){
      $inspectionSchedule->adoptionApplication->user->notify(new ApplicantInspectionScheduleNotification($inspectionSchedule));
    }
    //notify the inspector
    if($inspectionSchedule->user){
      $inspectionSchedule->user->notify(new InspectorInspectionScheduleNotification($inspectionSchedule));
    }
  }

  /**
    * Handle the InspectionSchedule "updated" event.
  */
  public function updated(InspectionSchedule $inspectionSchedule): void
  {
    //
  }

  /**
    * Handle the InspectionSchedule "deleted" event.
  */
  public function deleted(InspectionSchedule $inspectionSchedule): void
  {
    //
  }

  /**
    * Handle the InspectionSchedule "restored" event.
  */
  public function restored(InspectionSchedule $inspectionSchedule): void
  {
    //
  }

  /**
    * Handle the InspectionSchedule "force deleted" event.
  */
  public function forceDeleted(InspectionSchedule $inspectionSchedule): void
  {
    //
  }
}
