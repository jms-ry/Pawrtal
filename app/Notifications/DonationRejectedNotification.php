<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Donation;
class DonationRejectedNotification extends Notification
{
  use Queueable;

  /**
    * Create a new notification instance.
  */
  public function __construct(public Donation $donation)
  {}

  public function via($notifiable): array
  {
    return ['database', 'mail'];
  }
  
  /**
    * Get the array representation of the notification.
    *
    * @return array<string, mixed>
  */
  public function toArray($notifiable): array
  {
    return [
      'donation_id' => $this->donation->id,
      'donation_type' => $this->donation->donation_type,
      'message' => 'Your '. $this->donation->donation_type . ' donation has been rejected. Check "My Donation History" for more details.',
      'rejected_at' => now()->toDateTimeString(),
    ];
  }

  public function toMail($notifiable): MailMessage
  {
    $donationType = ucfirst($this->donation->donation_type);

    return (new MailMessage)
      ->subject("Update on Your {$donationType} Donation")
      ->greeting("Hello, {$notifiable->first_name}!")
      ->line("We regret to inform you that your {$donationType} donation to Ormoc Stray Oasis could not be accepted at this time.")
      ->line($this->donation->donation_type === 'monetary'
        ? "Amount: ₱" . number_format($this->donation->amount, 2)
        : "Item: " . ucfirst($this->donation->item_description)
      )
      ->line('If you have questions or would like to discuss this further, please feel free to reach out to us directly.')
      ->action('View My Donations', url('users/my-donations'))
      ->line('We truly appreciate your willingness to support the animals in our care. We hope to hear from you again soon.')
    ->salutation('With gratitude, Ormoc Stray Oasis 🐶🐱');
  }
}
