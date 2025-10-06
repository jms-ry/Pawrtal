<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Donation;

class DonationArchivedNotification extends Notification
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
      'donation_status' => $this->donation->status,
      'donation_type' => $this->donation->donation_type,
      'message' => 'Your donation has been archived',
      'archived_at' => now()->toDateTimeString(),
    ];
  }
}
