<?php

namespace App\Console\Commands;

use App\Models\Donation;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CleanupExpiredDonations extends Command
{
  protected $signature = 'donations:cleanup-expired';
  protected $description = 'Cancel monetary donations with pending payment status older than 24 hours';

  public function handle()
  {
    $expiredDonations = Donation::where('donation_type', 'monetary')
      ->where('payment_status', 'pending')
      ->where('donation_date', '<', Carbon::now()->subMinutes(30))
    ->get();

    $count = $expiredDonations->count();

    if ($count === 0) {
      $this->info('No expired donations found.');
      Log::info('Donation cleanup: No expired donations');
      return 0;
    }

    foreach ($expiredDonations as $donation) {
      $donation->update([
        'payment_status' => 'failed',
        'status' => 'cancelled',
      ]);
            
      $this->info("Cancelled donation ID: {$donation->id}");
    }

    $this->info("Cancelled {$count} expired donation(s).");
    Log::info("Donation cleanup: Cancelled {$count} expired donations");
        
    return 0;
  }
}