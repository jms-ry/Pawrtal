<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use App\Models\Report;
use App\Observers\ReportObserver;
class AppServiceProvider extends ServiceProvider
{
  /**
    * Register any application services.
  */
  public function register(): void
  {
    //
  }

  /**
    * Bootstrap any application services.
  */
  public function boot(): void
  {
    ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
      return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
    });
    Inertia::share([
      'auth' => function () {
        return [
          'user' => auth()->user() ? [
            'id' => auth()->user()->id,
            'name' => auth()->user()->fullName(),
            'isAdminOrStaff' => auth()->user()->isAdminOrStaff(),
          ] : null,
        ];
      },
    ]);

    Report::observe(ReportObserver::class);
  }
}
