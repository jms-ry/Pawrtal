<?php

namespace App\Notifications;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
class ReportAlertNotification extends Notification
{
  use Queueable;

  protected $report;
  protected $alerter;

  public function __construct(Report $report, $alerter)
  {
    $this->report = $report;
    $this->alerter = $alerter;
  }

  public function via(object $notifiable): array
  {
    return ['database', 'mail'];
  }

  public function toArray(object $notifiable): array
  {
    $reportDetails = $this->report->type . ' ' . strtolower($this->report->species);
    
    // Add animal name if it's a lost report
    if ($this->report->type === 'lost' && $this->report->animal_name) {
      $reportDetails .= ' named ' . $this->report->animal_name;
    }
    
    return [
      'report_id' => $this->report->id,
      'report_type' => $this->report->type,
      'alerter_id' => $this->alerter->id,
      'alerter_name' => $this->alerter->first_name . ' ' . $this->alerter->last_name,
      'alerter_email' => $this->alerter->email,
      'alerter_contact_number' => $this->alerter->contact_number,
      'message' => $this->alerter->first_name . ' ' . $this->alerter->last_name . ' has information about your ' . $reportDetails . ' report. You can contact them to discuss further details.',
    ];
  }

  public function toMail($notifiable): MailMessage
  {
    $species = ucfirst(strtolower($this->report->species));
    $type = $this->report->type === 'lost' ? 'Lost' : 'Found';
    $alerterName = $this->alerter->first_name . ' ' . $this->alerter->last_name;

    // Build subject line and message separately for natural phrasing
    if ($this->report->type === 'lost' && $this->report->animal_name) {
      $animalName = ucfirst($this->report->animal_name);
      $subject = "Someone Has Information About Your Lost {$species}, {$animalName}!";
      $reportRef = "your lost {$species}, {$animalName}";
    } else {
      $subject = "Someone Has Information About Your {$type} {$species} Report!";
      $reportRef = "your {$type} {$species} report";
    }

    return (new MailMessage)
      ->subject($subject)
      ->greeting("Hello, {$notifiable->first_name}!")
      ->line("{$alerterName} has information about {$reportRef} and would like to get in touch with you.")
      ->line("Here are their contact details:")
      ->line("📧 Email: {$this->alerter->email}")
      ->line("📞 Contact Number: " . ($this->alerter->contact_number ?? 'Not provided'))
      ->action('View My Report', url("users/my-reports/"))
      ->line('We recommend reaching out to them as soon as possible — they may have helpful information about your animal.')
    ->salutation('With care, Ormoc Stray Oasis 🐶🐱');
  }
}