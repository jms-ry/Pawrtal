<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInspectionScheduleRequest;
use App\Http\Requests\UpdateInspectionScheduleRequest;
use App\Models\InspectionSchedule;
use Illuminate\Http\Request;
use App\Models\AdoptionApplication;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Access\AuthorizationException;

class InspectionScheduleController extends Controller
{
  /**
    * Display a listing of the resource.
  */
  public function index()
  {
    //
  }

  /**
    * Show the form for creating a new resource.
  */
  public function create()
  {
    //
  }

  /**
    * Store a newly created resource in storage.
  */
  public function store(StoreInspectionScheduleRequest $request)
  {
    $application = AdoptionApplication::findOrFail($request->application_id);
    $this->authorize('create', [InspectionSchedule::class, $application]);

    $requestData = $request->validated();

    try {
      DB::transaction(function () use ($application, $requestData) {
        $lockedApplication = AdoptionApplication::lockForUpdate()->findOrFail($application->id);

        // Re-check policy on the freshly locked row
        $this->authorize('create', [InspectionSchedule::class, $lockedApplication]);

        InspectionSchedule::create($requestData);

         $lockedApplication->update(['status' => 'under_review']);
      });
    } catch (AuthorizationException $e) {
      return redirect()->back()->with(
        'warning',
        'An inspection can no longer be scheduled for this application. It may have been cancelled, already scheduled, or already processed.'
      );
    }

    return redirect()->back()->with('success', 'Inspection schedule has been scheduled.');
  }

  /**
    * Display the specified resource.
  */
  public function show(InspectionSchedule $inspectionSchedule)
  {
    //
  }

  /**
    * Show the form for editing the specified resource.
  */
  public function edit(InspectionSchedule $inspectionSchedule)
  {
    //
  }

  /**
    * Update the specified resource in storage.
  */
  public function update(UpdateInspectionScheduleRequest $request, InspectionSchedule $inspectionSchedule)
  {
    $requestData = $request->validated();

    if ($request->status === 'done') {
      $this->authorize('done', $inspectionSchedule);

      try {
        DB::transaction(function () use ($inspectionSchedule, $requestData) {
          $lockedSchedule = InspectionSchedule::lockForUpdate()->findOrFail($inspectionSchedule->id);

          $this->authorize('done', $lockedSchedule);

          $lockedSchedule->update($requestData);
        });
      } catch (AuthorizationException $e) {
        return redirect()->back()->with(
          'warning',
          'This inspection schedule can no longer be marked done. It may have been cancelled or already completed.'
        );
       }

      return redirect()->back()->with('success', 'Inspection schedule has been marked done.');
    }

    if ($request->status === 'cancelled') {
      $this->authorize('cancel', $inspectionSchedule);

      try {
        DB::transaction(function () use ($inspectionSchedule, $requestData) {
          // Lock order: application first, then schedule (same as store())
          $lockedApplication = AdoptionApplication::lockForUpdate()->findOrFail($inspectionSchedule->application_id);
          $lockedSchedule = InspectionSchedule::lockForUpdate()->findOrFail($inspectionSchedule->id);

          $this->authorize('cancel', $lockedSchedule);

          $lockedSchedule->update($requestData);

          // Only revert if staff hasn't already decided on the application
          if ($lockedApplication->status === 'under_review') {
            $lockedApplication->update(['status' => 'pending']);
          }
        });
      } catch (AuthorizationException $e) {
        return redirect()->back()->with(
          'warning',
          'This inspection schedule can no longer be cancelled. It may have been completed or already cancelled.'
        );
      }

      return redirect()->back()->with('warning', 'Inspection schedule has been cancelled.');
    }

    abort(422);
  }

  /**
    * Remove the specified resource from storage.
  */
  public function destroy(InspectionSchedule $inspectionSchedule)
  {
    //
  }
}
