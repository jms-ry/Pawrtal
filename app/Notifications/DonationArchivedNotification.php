<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Donation;
use App\Models\User;

class DonationArchivedNotification extends Notification
{
  use Queueable;

  protected $donation;
  protected $archivedBy;
  /**
    * Create a new notification instance.
  */
  public function __construct(Donation $donation, User $archivedBy)
  {
    $this->donation = $donation;
    $this->archivedBy = $archivedBy;
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
    $archivedByOwner = $this->archivedBy->id === $notifiable->id;

    if($archivedByOwner) {
      $message = "You archived your {$this->donation->donation_type} donation. Check \"My Donation History\" for more details.";
    } else {
      $archiverName = $this->archivedBy->fullName();
      $message = "Your {$this->donation->donation_type} donation has been archived by {$archiverName}. Check \"My Donation History\" for more details.";
    }

    return [
      'donation_id' => $this->donation->id,
      'donation_type' => $this->donation->donation_type,
      'message' => $message,
      'archived_by_id' => $this->archivedBy->id,
      'archived_by_name' => $this->archivedBy->fullName(),
      'archived_by_owner' => $archivedByOwner,
      'archived_at' => now()->toDateTimeString(),
    ];
  }
}
