<?php

namespace App\Notifications;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReportArchivedNotification extends Notification
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
      'message' => 'Your '. $reportDetails . ' report has been archived. Check "My Reports" for more details.',
      'archived_at' => now()->toDateTimeString(),
    ];
  }
}
