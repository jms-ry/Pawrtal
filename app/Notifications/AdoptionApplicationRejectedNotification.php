<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\AdoptionApplication;
class AdoptionApplicationRejectedNotification extends Notification
{
  use Queueable;

  /**
    * Create a new notification instance.
  */
  public function __construct(
    public AdoptionApplication $adoptionApplication
  ){}

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
    return [
      'application_id' => $this->adoptionApplication->id,
      'rescue_name' => $this->adoptionApplication->rescue->name,
      'message' => 'Your adoption application has been rejected. Check "My Adoption Applications" for more details.',
      'rejected_at' => now()->toDateTimeString(),
    ];
  }
}
