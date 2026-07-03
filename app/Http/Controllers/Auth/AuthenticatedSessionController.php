<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthenticatedSessionController extends Controller
{
  /**
    * Handle an incoming authentication request.
  */

  protected $redirectTo = '/dashboard';
  public function store(LoginRequest $request): RedirectResponse
  {
    $request->authenticate();

    $request->session()->regenerate();

    $user = Auth::user();

    if($user->isAdminOrStaff())
    {
      Session::flash('success', 'You are logged in as '.$user->getRole(). '!');
      return redirect()->intended($this->redirectTo);
    }
    else
    {
      Session::flash('success', 'You logged in successfully!');
      return redirect()->intended('/');
    }
  }

  /**
    * Destroy an authenticated session.
  */
  public function destroy(Request $request): RedirectResponse
  {
    Auth::guard('web')->logout();

    $request->session()->invalidate();

    $request->session()->regenerateToken();

    Session::flash('warning', 'You logged out successfully!');
    return redirect()->intended('/');
  }
}
