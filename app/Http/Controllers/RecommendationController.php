<?php

namespace App\Http\Controllers;

use App\Services\RecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecommendationController extends Controller
{
  protected $recommendationService;

  public function __construct(RecommendationService $recommendationService)
  {
    $this->recommendationService = $recommendationService;
  }

  /**
    * Get personalized rescue recommendations
  */
  public function getMatches(Request $request)
  {
    $validated = $request->validate([
      'size' => 'required|in:small,medium,large,any',
      'age_preference' => 'required|in:puppy,young,adult,senior,any',
      'sex' => 'required|in:male,female,any,no_preference',
      'energy_level' => 'required|in:low,moderate,high,any',
      'maintenance_level' => 'required|in:low,moderate,high,any',
      'temperament' => 'nullable|in:friendly,calm,playful,protective,any',
    ]);

    $user = Auth::user();

    $household = [
      'house_structure' => $user->household->house_structure ?? 'unknown',
      'household_members' => $user->household->household_members ?? 1,
      'have_children' => $user->household->have_children ?? false,
      'number_of_children' => $user->household->number_of_children ?? 0,
      'has_other_pets' => $user->household->has_other_pets ?? false,
      'current_pets' => $user->household->current_pets ?? '',
      'number_of_current_pets' => $user->household->number_of_current_pets ?? 0,
    ];

    $recommendations = $this->recommendationService->getRecommendations(
      $validated,
      $household
    );

    return response()->json([
      'success' => true,
      'count' => count($recommendations),
      'matches' => $recommendations,
    ]);
  }
}