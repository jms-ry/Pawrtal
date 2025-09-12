<?php

namespace App\Http\Controllers;

use App\Models\Rescue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdoptionController extends Controller
{
  public function index(Request $request)
  {
    $search = $request->get('search');
    $sexFilter = $request->get('sex');
    $sizeFilter = $request->get('size');

    $adoptables = Rescue::query()
      ->where('adoption_status','available')
      ->when($search, function($query,$search){
        return $query->whereRaw('LOWER(name) LIKE LOWER(?)', ['%' . $search . '%']);
      })
      ->when($sexFilter, function ($query, $sexFilter) {
        return $query->where('sex', $sexFilter);
      })
      ->when($sizeFilter, function ($query, $sizeFilter) {
        return $query->where('size', $sizeFilter);
      })
      ->paginate(9)
      ->withQueryString();

    $user = Auth::user();

    $user = $user?->load('address', 'household');

    return Inertia::render('Adoption/Index',[
      'adoptables' => $adoptables,
      'user' => $user ? [
        'isAdminOrStaff' => $user->isAdminOrStaff(),
        'id' => $user->id,
        'canAdopt' => $user->canAdopt(),
        'address' => $user->address,
        'household' => $user->household,
      ] : null,
      'filters' => [
        'search' => $search,
        'sex' => $sexFilter,
        'size' => $sizeFilter,
      ],
    ]);
  }
}
