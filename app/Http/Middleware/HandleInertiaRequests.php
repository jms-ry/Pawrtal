<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Illuminate\Support\Facades\Auth;

class HandleInertiaRequests extends Middleware
{
  /**
    * The root template that's loaded on the first page visit.
    *
    * @see https://inertiajs.com/server-side-setup#root-template
    *
    * @var string
  */
  protected $rootView = 'app';

  /**
    * Determines the current asset version.
    *
    * @see https://inertiajs.com/asset-versioning
  */
  public function version(Request $request): ?string
  {
    return parent::version($request);
  }

  /**
    * Define the props that are shared by default.
    *
    * @see https://inertiajs.com/shared-data
    *
    * @return array<string, mixed>
  */
  public function share(Request $request)
  {
    return array_merge(parent::share($request), [
      'flash' => [
        'success' => fn () => $request->session()->get('success'),
        'error' => fn () => $request->session()->get('error'),
        'warning' => fn () => $request->session()->get('warning'),
        'info' => fn () => $request->session()->get('info'),
      ],

      // 🔔 Notifications
      'unreadNotifications' => fn () => Auth::check()
        ? Auth::user()->unreadNotifications()->take(10)->get(['id', 'data', 'created_at'])
      : [],
      'unreadCount' => fn () => Auth::check()
      ? Auth::user()->unreadNotifications()->count()
      : 0,

      // 💬 Messages
      'unreadMessages' => fn () => Auth::check()
        ? \App\Models\Message::whereHas('conversation', function ($query) {
        $query->where(function ($q) {
          $userId = Auth::id();
          $q->where('participant1_id', $userId)
            ->orWhere('participant2_id', $userId);
          });
        })
        ->where('sender_id', '!=', Auth::id())
        ->where('status', '!=', 'read')
        ->latest()
        ->take(10)
        ->get(['id', 'content', 'sender_id', 'conversation_id', 'created_at'])
      : [],

      'unreadMessagesCount' => fn () => Auth::check()
        ? \App\Models\Message::whereHas('conversation', function ($query) {
        $query->where(function ($q) {
          $userId = Auth::id();
          $q->where('participant1_id', $userId)
            ->orWhere('participant2_id', $userId);
            });
        })
        ->where('sender_id', '!=', Auth::id())
        ->where('status', '!=', 'read')
        ->count()
      : 0,
    ]);
  }

}
