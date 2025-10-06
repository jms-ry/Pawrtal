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
    return [
      'report_id' => $this->report->id,
      'report_type' => $this->report->type,
      'message' => 'Your report has been archived',
      'archived_at' => now()->toDateTimeString(),
    ];
  }
}
