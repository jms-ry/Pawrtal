<?php

namespace App\Notifications;

use App\Models\Report;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReportArchivedNotification extends Notification
{
  use Queueable;

  protected $report;
  protected $archivedBy;

  public function __construct(Report $report, User $archivedBy)
  {
    $this->report = $report;
    $this->archivedBy = $archivedBy;
  }

  public function via(object $notifiable): array
  {
    return ['database'];
  }

  public function toArray(object $notifiable): array
  {
    $reportDetails = $this->report->type . ' ' . strtolower($this->report->species);
    $archivedByOwner = $this->archivedBy->id === $notifiable->id;
        
    if ($archivedByOwner) {
      $message = "You archived your {$reportDetails} report. Check \"My Reports\" for more details.";
    } else {
      $archiverName = $this->archivedBy->fullName();
      $message = "Your {$reportDetails} report has been archived by {$archiverName}. Check \"My Reports\" for more details.";
    }
        
    return [
      'report_id' => $this->report->id,
      'report_type' => $this->report->type,
      'message' => $message,
      'archived_by_id' => $this->archivedBy->id,
      'archived_by_name' => $this->archivedBy->fullName(),
      'archived_by_owner' => $archivedByOwner,
      'archived_at' => now()->toDateTimeString(),
    ];
  }
}