<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\InspectionSchedule;

class DailyInspectionReminderNotification extends Notification
{
  use Queueable;

  /**
    * Create a new notification instance.
  */
  public function __construct(
    public InspectionSchedule $inspectionSchedule
  ) {}

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
      'message' => 'You have an inspection scheduled for TODAY. Check "My Schedules" for details.',
      'type' => 'inspection_reminder',
    ];
  }
}