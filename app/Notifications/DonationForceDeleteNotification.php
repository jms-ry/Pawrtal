<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Donation;

class DonationForceDeleteNotification extends Notification
{
    use Queueable;

  /**
    * Create a new notification instance.
  */
  public function __construct(public Donation $donation)
  {}

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
    return [
      'donation_id' => $this->donation->id,
      'donation_type' => $this->donation->donation_type,
      'message' => 'Your '.$this->donation->status. ' '. $this->donation->donation_type . ' donation has been permanently deleted.',
      'force_deleted_at' => now()->toDateTimeString(),
    ];
  }
}
