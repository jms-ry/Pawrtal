<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\AdoptionApplication;
use App\Models\User;

class AdoptionApplicationArchivedNotification extends Notification
{
  use Queueable;

  protected $adoptionApplication;
  protected $archivedBy;
  /**
    * Create a new notification instance.
  */
  public function __construct(AdoptionApplication $adoptionApplication, User $archivedBy)
  {
    $this->adoptionApplication = $adoptionApplication;
    $this->archivedBy = $archivedBy;
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
    $archivedByOwner = $this->archivedBy->id === $notifiable->id;

    if($archivedByOwner) {
      $message = "You archived your {$this->adoptionApplication->status} for {$this->adoptionApplication->rescue->name}. Check \"My Adoption Applications\" for more details.";
    } else {
      $archiverName = $this->archivedBy->fullName();
      $message = "Your {$this->adoptionApplication->status} for {$this->adoptionApplication->rescue->name} has been archived by {$archiverName}. Check \"My Adoption Applications\" for more details.";
    }

    return [
      'application_id' => $this->adoptionApplication->id,
      'rescue_name' => $this->adoptionApplication->rescue->name,
      'application_status' => $this->adoptionApplication->status,
      'message' => $message,
      'archived_by_id' => $this->archivedBy->id,
      'archived_by_name' => $this->archivedBy->fullName(),
      'archived_by_owner' => $archivedByOwner,
      'archived_at' => now()->toDateTimeString(),
    ];
  }
}
