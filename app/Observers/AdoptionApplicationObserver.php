<?php

namespace App\Observers;

use App\Models\AdoptionApplication;
use App\Notifications\AdoptionApplicationApprovedNotification;
use App\Notifications\AdoptionApplicationArchivedNotification;
use App\Notifications\AdoptionApplicationCancelledNotification;
use App\Notifications\AdoptionApplicationRejectedNotification;
use App\Notifications\AdoptionApplicationRestoredNotification;

class AdoptionApplicationObserver
{
  /**
    * Handle the AdoptionApplication "created" event.
  */
  public function created(AdoptionApplication $adoptionApplication): void
  {
    //
  }

  /**
    * Handle the AdoptionApplication "updated" event.
  */
  public function updated(AdoptionApplication $adoptionApplication): void
  {
    if($adoptionApplication->user){
      if($adoptionApplication->status === 'cancelled'){
        $adoptionApplication->user->notify(new AdoptionApplicationCancelledNotification($adoptionApplication));
      }

      if($adoptionApplication->status === 'approved'){
        $adoptionApplication->user->notify(new AdoptionApplicationApprovedNotification($adoptionApplication));
      }

      if($adoptionApplication->status === 'rejected'){
        $adoptionApplication->user->notify(new AdoptionApplicationRejectedNotification($adoptionApplication));
      }
    }
  }

  /**
    * Handle the AdoptionApplication "deleted" event.
  */
  public function deleted(AdoptionApplication $adoptionApplication): void
  {
    if(!$adoptionApplication->isForceDeleting() && $adoptionApplication->user){
      $adoptionApplication->user->notify(new AdoptionApplicationArchivedNotification($adoptionApplication));
    }
  }

  /**
    * Handle the AdoptionApplication "restored" event.
  */
  public function restored(AdoptionApplication $adoptionApplication): void
  {
    if($adoptionApplication->user){
      $adoptionApplication->user->notify(new AdoptionApplicationRestoredNotification($adoptionApplication));
    }
  }

  /**
    * Handle the AdoptionApplication "force deleted" event.
  */
  public function forceDeleted(AdoptionApplication $adoptionApplication): void
  {
    //
  }
}
