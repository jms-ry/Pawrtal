<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Session;

class RegisteredUserController extends Controller
{
  /**
    * Handle an incoming registration request.
    *
    * @throws \Illuminate\Validation\ValidationException
  */
  public function store(Request $request): RedirectResponse
  {
    $request->validate([
      'first_name' => ['required', 'string', 'max:255'],
      'last_name' => ['required', 'string', 'max:255'],
      'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
      'contact_number' => [
        'required',
        'regex:/^09\d{9}$/', // must start with 09 and followed by 9 digits (total of 11)
      ],
      'password' => ['required', 'confirmed', Rules\Password::defaults()],
    ]);

    $user = User::create([
      'first_name' => $request->first_name,
      'last_name' => $request->last_name,
      'contact_number' => $request->contact_number,
      'email' => $request->email,
      'password' => Hash::make($request->string('password')),
    ]);

    event(new Registered($user));

    Auth::login($user);

    Session::flash('success', 'You have successfully created an account!');
    return redirect('/');
  }
}
