<?php

namespace App\Notifications;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

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
    return ['database'];
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
}