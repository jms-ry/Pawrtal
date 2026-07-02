<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\InspectionSchedule;
class ApplicantInspectionScheduleNotification extends Notification
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
      'inspector_name' => $this->inspectionSchedule->inspectorName(),
      'inspection_date' => $this->inspectionSchedule->inspectionDate(),
      'inspection_location' => $this->inspectionSchedule->inspectionLocation(),
      'message' => 'Inspection schedule of your adoption application has been scheduled. Check "My Adoption Applications" for more details.',
    ];
  }

  public function toMail($notifiable): MailMessage
  {
    $inspectorName = $this->inspectionSchedule->inspectorName();
    $inspectionDate = $this->inspectionSchedule->inspectionDate();
    $inspectionLocation = $this->inspectionSchedule->inspectionLocation();

    return (new MailMessage)
      ->subject("Your Home Inspection Has Been Scheduled!")
      ->greeting("Hello, {$notifiable->first_name}!")
      ->line("Great news! Your adoption application has progressed and a home inspection has been scheduled. Here are the details:")
      ->line("👤 Inspector: {$inspectorName}")
      ->line("📅 Inspection Date: {$inspectionDate}")
      ->line("📍 Location: {$inspectionLocation}")
      ->action('View My Applications', url('users/my-adoption-applications'))
      ->line('Please make sure someone is available at home on the scheduled date to accommodate the inspector.')
      ->line('If you have any questions or concerns about the inspection, feel free to reach out to us.')
    ->salutation('With care, Ormoc Stray Oasis 🐶🐱');
  }
}
