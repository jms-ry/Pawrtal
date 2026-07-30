<?php

namespace App\Observers;

use App\Models\Donation;
use App\Notifications\DonationAcceptedNotification;
use App\Notifications\DonationArchivedNotification;
use App\Notifications\DonationCancelledNotification;
use App\Notifications\DonationRejectedNotification;
use App\Notifications\DonationRestoredNotification;

class DonationObserver
{
  /**
    * Handle the Donation "created" event.
  */
  public function created(Donation $donation): void
  {
    //
  }

  /**
    * Handle the Donation "updated" event.
  */
  public function updated(Donation $donation): void
  {
    if($donation->wasChanged('status') && $donation->user){
      try{
        if($donation->status ==='cancelled'){
          $donation->user->notify(new DonationCancelledNotification($donation));
        }
        
        if($donation->status ==='accepted'){
          $donation->user->notify(new DonationAcceptedNotification($donation));
        }

        if($donation->status === 'rejected'){
          $donation->user->notify(new DonationRejectedNotification($donation));
        }
      } catch (\Exception $e) {
        // Handle the exception, e.g., log the error
        \Log::error('Failed to send donation notification: ' . $e->getMessage());
      }
      
    }
  }

  /**
    * Handle the Donation "deleted" event.
  */
  public function deleted(Donation $donation): void
  {
    
  }

  /**
    * Handle the Donation "restored" event.
  */
  public function restored(Donation $donation): void
  {
    
  }

  /**
    * Handle the Donation "force deleted" event.
  */
  public function forceDeleted(Donation $donation): void
  {
    //
  }
}
