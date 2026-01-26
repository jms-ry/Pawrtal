<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportAlert;
use App\Notifications\ReportAlertNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportAlertController extends Controller
{
  public function store(Report $report)
  {
    // Make sure user is authenticated
    if (!Auth::check()) {
      return back()->with('error', 'You must be logged in to alert the owner.');
    }

    // Make sure user is not alerting their own report
    if ($report->user_id === Auth::id()) {
      return back()->with('error', 'You cannot alert yourself.');
    }

    // Check if user has already alerted this report
    if ($report->hasBeenAlertedBy(Auth::user())) {
      return back()->with('error', 'You have already alerted the owner of this report.');
    }

    // Record the alert
    ReportAlert::create([
      'user_id' => Auth::id(),
      'report_id' => $report->id,
      'alerted_at' => now(),
    ]);
    
    // Send notification to report owner
    $report->user->notify(new ReportAlertNotification($report, Auth::user()));

    return back()->with('success', 'The report owner has been notified!');
  }
}