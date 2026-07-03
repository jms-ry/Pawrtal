<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
  // Show the forgot password form
  public function create(): View
  {
    return view('auth.forgot-password');
  }

  // Handle the form submission
  public function store(Request $request): RedirectResponse
  {
    $request->validate([
      'email' => ['required', 'email'],
    ]);

    $status = Password::sendResetLink(
      $request->only('email')
    );

    if ($status == Password::RESET_LINK_SENT) {
      return back()->with('success', 'Password reset link has been sent to your email.');
    }

    return back()->withInput($request->only('email'))->with('error', 'We could not find an account with that email address.');
  }
}