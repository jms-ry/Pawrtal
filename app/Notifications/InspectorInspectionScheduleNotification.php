<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\InspectionSchedule;
class InspectorInspectionScheduleNotification extends Notification
{
  use Queueable;

  /**
    * Create a new notification instance.
  */
  public function __construct(
    public InspectionSchedule $inspectionSchedule
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

  /**
    * Get the array representation of the notification.
    *
    * @return array<string, mixed>
  */
  public function toArray($notifiable): array
  {
    return [
      'inspection_id' => $this->inspectionSchedule->id,
      'application_id' => $this->inspectionSchedule->adoptionApplication->id,
      'applicant_name' => $this->inspectionSchedule->adoptionApplication->user->fullName(),
      'inspection_date' => $this->inspectionSchedule->inspectionDate(),
      'inspection_location' => $this->inspectionSchedule->inspectionLocation(),
      'message' => 'You have been scheduled to conduct a home inspection for an adoption applicant.',
    ];
  }
}
