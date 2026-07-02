<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\InspectionSchedule;
use Illuminate\Notifications\Messages\MailMessage;
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
      'message' => 'You have an inspection scheduled for TODAY. Check "My Schedules" for details.',
      'type' => 'inspection_reminder',
    ];
  }

  public function toMail($notifiable): MailMessage
  {
    $applicantName = $this->inspectionSchedule->adoptionApplication->user?->fullName() ?? 'Unknown Applicant';
    $inspectionDate = $this->inspectionSchedule->inspectionDate();
    $inspectionLocation = $this->inspectionSchedule->inspectionLocation();

    return (new MailMessage)
      ->subject("Reminder: You Have a Home Inspection Scheduled Today!")
      ->greeting("Hello, {$notifiable->first_name}!")
      ->line("This is a friendly reminder that you have a home inspection scheduled for TODAY. Here are the details:")
      ->line("👤 Applicant: {$applicantName}")
      ->line("📅 Inspection Date: {$inspectionDate}")
      ->line("📍 Location: {$inspectionLocation}")
      ->action('View My Schedules', url('users/my-schedules'))
      ->line('Please make sure you are available and prepared for the inspection. If you have any concerns, coordinate with the admin or staff immediately.')
    ->salutation('With regards, Ormoc Stray Oasis 🐶🐱');
  }
}