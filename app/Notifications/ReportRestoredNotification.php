<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Report;
use App\Models\User;

class ReportRestoredNotification extends Notification
{
  use Queueable;

  protected $report;
  protected $restoredBy;

  public function __construct(Report $report, User $restoredBy)
  {
    $this->report = $report;
    $this->restoredBy = $restoredBy;
  }

  public function via(object $notifiable): array
  {
    return ['database'];
  }

  public function toArray(object $notifiable): array
  {
    $reportDetails = $this->report->type . ' ' . strtolower($this->report->species);
    $restoredByOwner = $this->restoredBy->id === $notifiable->id;
        
    if ($restoredByOwner) {
      $message = "You restored your {$reportDetails} report. Check \"My Reports\" for more details.";
    } else {
      $restorerName = $this->restoredBy->fullName();
      $message = "Your {$reportDetails} report has been restored by {$restorerName}. Check \"My Reports\" for more details.";
    }
        
    return [
      'report_id' => $this->report->id,
      'report_type' => $this->report->type,
      'message' => $message,
      'restored_by_id' => $this->restoredBy->id,
      'restored_by_name' => $this->restoredBy->fullName(),
      'restored_by_owner' => $restoredByOwner,
      'restored_at' => now()->toDateTimeString(),
    ];
  }
}
