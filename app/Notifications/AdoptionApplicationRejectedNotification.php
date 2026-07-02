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
    return ['database','mail'];
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

  public function toMail($notifiable): MailMessage
  {
    $rescueName = ucfirst($this->adoptionApplication->rescue->name);

    return (new MailMessage)
      ->subject("Update on Your Adoption Application for {$rescueName}")
      ->greeting("Hello, {$notifiable->first_name}!")
      ->line("Thank you for your interest in adopting {$rescueName} and for taking the time to submit an application.")
      ->line("After careful consideration, we regret to inform you that your adoption application for {$rescueName} has not been approved at this time.")
      ->line("We encourage you to not lose heart — there are many other animals in our care who are looking for a loving home. We welcome you to browse our adoptable rescues and apply again.")
      ->action('View Adoptable Rescues', url('/adoption'))
      ->line('If you have any questions or would like feedback on your application, please feel free to reach out to us directly.')
    ->salutation('With care, Ormoc Stray Oasis 🐶🐱');
  }
}
