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
    return ['database','mail'];
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
      'message' => 'You have been scheduled to conduct a home inspection for an adoption applicant. Check "My Schedules" for more details.',
    ];
  }

  public function toMail($notifiable): MailMessage
  {
    $applicantName = $this->inspectionSchedule->adoptionApplication->user->fullName();
    $inspectionDate = $this->inspectionSchedule->inspectionDate();
    $inspectionLocation = $this->inspectionSchedule->inspectionLocation();

    return (new MailMessage)
      ->subject("New Home Inspection Assignment")
      ->greeting("Hello, {$notifiable->first_name}!")
      ->line("You have been assigned to conduct a home inspection for an adoption applicant. Here are the details:")
      ->line("👤 Applicant: {$applicantName}")
      ->line("📅 Inspection Date: {$inspectionDate}")
      ->line("📍 Location: {$inspectionLocation}")
      ->action('View My Schedules', url('users/my-schedules'))
      ->line('Please make sure to be available on the scheduled date. If you have any concerns, coordinate with the admin or staff immediately.')
    ->salutation('With regards, Ormoc Stray Oasis 🐶🐱');
  }
}
