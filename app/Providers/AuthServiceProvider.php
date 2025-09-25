<?php

namespace App\Providers;

use Gate;
use Illuminate\Support\ServiceProvider;

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
    \App\Models\Rescue::class => \App\Policies\RescuePolicy::class,
  ];

}
