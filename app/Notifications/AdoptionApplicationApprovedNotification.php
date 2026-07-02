<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\AdoptionApplication;
class AdoptionApplicationApprovedNotification extends Notification
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
    return ['database','mail'];
  }

  public function toArray($notifiable): array
  {
    return [
      'application_id' => $this->adoptionApplication->id,
      'rescue_name' => $this->adoptionApplication->rescue->name,
      'message' => 'Your adoption application has been approved. Check "My Adoption Applications" for more details.',
      'approved_at' => now()->toDateTimeString(),
    ];
  }

  public function toMail($notifiable): MailMessage
  {
    $rescueName = ucfirst($this->adoptionApplication->rescue->name);

    return (new MailMessage)
      ->subject("Congratulations! Your Adoption Application Has Been Approved! 🎉")
      ->greeting("Hello, {$notifiable->first_name}!")
      ->line("We are thrilled to inform you that your adoption application for {$rescueName} has been approved!")
      ->line("Please coordinate with Ormoc Stray Oasis for the next steps regarding the adoption process and when you can pick up your new companion.")
      ->action('View My Applications', url('users/my-adoption-applications'))
      ->line("We are so excited for you and {$rescueName} to start this new journey together. Thank you for giving a rescue animal a loving home!")
    ->salutation('With joy, Ormoc Stray Oasis 🐶🐱');
  }
}
