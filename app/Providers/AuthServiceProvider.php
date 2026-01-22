<?php

namespace App\Providers;

use Gate;
use Illuminate\Support\ServiceProvider;
use App\Models\Address;
use App\Models\Household;
use App\Policies\AddressPolicy;
use App\Models\Rescue;
use App\Models\User;
use App\Models\Donation;
use App\Models\InspectionSchedule;
use App\Models\Report;
use App\Policies\AdoptionApplicationPolicy;
use App\Policies\DonationPolicy;
use App\Policies\HouseholdPolicy;
use App\Policies\InspectionSchedulePolicy;
use App\Policies\ReportPolicy;
use App\Policies\RescuePolicy;
use App\Policies\UserPolicy;

class AuthServiceProvider extends ServiceProvider
{
  /**
    * Register services.
  */
  public function register(): void
  {
    //
  }

  /**
    * Bootstrap services.
  */
  public function boot(): void
  {
    Gate::define('admin-staff-access', function ($user) {
      return $user->isAdminOrStaff();
    });
  }

  protected $policies = [
    Rescue::class => RescuePolicy::class,
    Address::class => AddressPolicy::class,
    Household::class => HouseholdPolicy::class,
    User::class => UserPolicy::class,
    Donation::class =>DonationPolicy::class,
    Report::class => ReportPolicy::class,
    AdoptionApplication::class => AdoptionApplicationPolicy::class,
    InspectionSchedule::class => InspectionSchedulePolicy::class,
  ];

}
