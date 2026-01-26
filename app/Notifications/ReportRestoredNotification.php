<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Report;

class ReportRestoredNotification extends Notification
{
  use Queueable;

  public function __construct(
    public Report $report
  ) {}

  public function via($notifiable): array
  {
    return ['database'];
  }

  public function toArray($notifiable): array
  {
    $reportDetails = $this->report->type . ' ' . strtolower($this->report->species);

    return [
      'report_id' => $this->report->id,
      'report_type' => $this->report->type,
      'message' => 'Your '. $reportDetails . ' report has been restored. Check "My Reports" for more details.',
      'restored_at' => now()->toDateTimeString(),
    ];
  }
}
