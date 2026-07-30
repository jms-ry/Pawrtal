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
    if($adoptionApplication->wasChanged('status') && $adoptionApplication->user){
      try {
        if($adoptionApplication->status === 'cancelled'){
          $adoptionApplication->user->notify(new AdoptionApplicationCancelledNotification($adoptionApplication));
        }
      } catch (\Exception $e) {
        // Handle the exception, e.g., log the error
        \Log::error('Failed to send adoption application notification: ' . $e->getMessage());
      }

      try{
        if($adoptionApplication->status === 'approved'){
          $adoptionApplication->user->notify(new AdoptionApplicationApprovedNotification($adoptionApplication));
        }
      } catch (\Exception $e) {
        // Handle the exception, e.g., log the error
        \Log::error('Failed to send adoption application notification: ' . $e->getMessage());
      }

      try{
        if($adoptionApplication->status === 'rejected'){
          $adoptionApplication->user->notify(new AdoptionApplicationRejectedNotification($adoptionApplication));
        }
      } catch (\Exception $e) {
        // Handle the exception, e.g., log the error
        \Log::error('Failed to send adoption application notification: ' . $e->getMessage());
      }
      
    }
  }

  /**
    * Handle the AdoptionApplication "deleted" event.
  */
  public function deleted(AdoptionApplication $adoptionApplication): void
  {
    
  }

  /**
    * Handle the AdoptionApplication "restored" event.
  */
  public function restored(AdoptionApplication $adoptionApplication): void
  {
   
  }

  /**
    * Handle the AdoptionApplication "force deleted" event.
  */
  public function forceDeleted(AdoptionApplication $adoptionApplication): void
  {
    //
  }
}
