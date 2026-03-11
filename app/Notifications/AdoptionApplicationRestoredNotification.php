<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\AdoptionApplication;
use App\Models\User;

class AdoptionApplicationRestoredNotification extends Notification
{
  use Queueable;

  protected $adoptionApplication;
  protected $restoredBy;
  /**
    * Create a new notification instance.
  */
  public function __construct(AdoptionApplication $adoptionApplication, User $restoredBy)
  {
    $this->adoptionApplication = $adoptionApplication;
    $this->restoredBy = $restoredBy;
  }

  /**
   * Get the notification's delivery channels.
    *
    * @return array<int, string>
  */
  public function via($notifiable): array
  {
    return ['database'];
  }

  public function toArray($notifiable): array
  {
    $restoredByOwner = $this->restoredBy->id === $notifiable->id;

    if($restoredByOwner) {
      $message = "You restored your {$this->adoptionApplication->status} adoption application for {$this->adoptionApplication->rescue->name}. Check \"My Adoption Applications\" for more details.";
    } else {
      $restorerName = $this->restoredBy->fullName();
      $message = "Your {$this->adoptionApplication->status} adoption application for {$this->adoptionApplication->rescue->name} has been restored by {$restorerName}. Check \"My Adoption Applications\" for more details.";
    }

    return [
      'application_id' => $this->adoptionApplication->id,
      'rescue_name' => $this->adoptionApplication->rescue->name,
      'message' => $message,
      'restored_at' => now()->toDateTimeString(),
    ];
  }
}
