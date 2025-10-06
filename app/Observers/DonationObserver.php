<?php

namespace App\Observers;

use App\Models\Donation;
use App\Notifications\DonationArchivedNotification;
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
    //
  }

  /**
    * Handle the Donation "deleted" event.
  */
  public function deleted(Donation $donation): void
  {
    if($donation->isForceDeleting() === false && $donation->user){
      $donation->user->notify(new DonationArchivedNotification($donation));
    }
  }

  /**
    * Handle the Donation "restored" event.
  */
  public function restored(Donation $donation): void
  {
    if($donation->user){
      $donation->user->notify(new DonationRestoredNotification($donation));
    }
  }

  /**
    * Handle the Donation "force deleted" event.
  */
  public function forceDeleted(Donation $donation): void
  {
    //
  }
}
