<?php

namespace App\Providers;

use Gate;
use Illuminate\Support\ServiceProvider;
use App\Models\Address;
use App\Policies\AddressPolicy;
use App\Models\Rescue;
use App\Policies\RescuePolicy;
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
  ];

}
