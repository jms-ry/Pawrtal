<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Donation;
class DonationAcceptedNotification extends Notification
{
  use Queueable;

  /**
    * Create a new notification instance.
  */
  public function __construct(public Donation $donation)
  {}

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
      'donation_id' => $this->donation->id,
      'donation_type' => $this->donation->donation_type,
      'message' => 'Your '. $this->donation->donation_type . ' donation has been accepted. Check "My Donation History" for more details.',
      'accepted_at' => now()->toDateTimeString(),
    ];
  }

  public function toMail($notifiable): MailMessage
  {
    $donationType = ucfirst($this->donation->donation_type);
    
    return (new MailMessage)
      ->subject("Your {$donationType} Donation Has Been Accepted! 🐾")
      ->greeting("Hello, {$notifiable->first_name}!")
      ->line("Great news! Your {$donationType} donation to Ormoc Stray Oasis has been accepted.")
      ->line($this->donation->donation_type === 'monetary' 
        ? "Amount: ₱" . number_format($this->donation->amount, 2)
        : "Item: " . ucfirst($this->donation->item_description)
      )
      ->action('View My Donations', url('users/my-donations'))
      ->line('Thank you for your generosity and support for the animals in our care!')
    ->salutation('With gratitude, Ormoc Stray Oasis 🐶🐱');
  }
}
