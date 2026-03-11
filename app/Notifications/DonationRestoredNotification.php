<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Donation;
use App\Models\User;

class DonationRestoredNotification extends Notification
{
  use Queueable;

  protected $donation;
  protected $restoredBy;
  /**
    * Create a new notification instance.
  */
  public function __construct(Donation $donation, User $restoredBy)
  {
    $this->donation = $donation;
    $this->restoredBy = $restoredBy;
  }

  public function via($notifiable): array
  {
    return ['database'];
  }
  
  /**
    * Get the array representation of the notification.
    *
    * @return array<string, mixed>
  */
  public function toArray($notifiable): array
  {
    $restoredByOwner = $this->restoredBy->id === $notifiable->id;

    if($restoredByOwner) {
      $message = "You restored your {$this->donation->donation_type} donation. Check \"My Donation History\" for more details.";
    } else {
      $restorerName = $this->restoredBy->fullName();
      $message = "Your {$this->donation->donation_type} donation has been restored by {$restorerName}. Check \"My Donation History\" for more details.";
    }

    return [
      'donation_id' => $this->donation->id,
      'donation_type' => $this->donation->donation_type,
      'message' => $message,
      'restored_by_id' => $this->restoredBy->id,
      'restored_by_name' => $this->restoredBy->fullName(),
      'restored_by_owner' => $restoredByOwner,
      'restored_at' => now()->toDateTimeString(),
    ];
  }
}