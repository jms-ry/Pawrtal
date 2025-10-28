<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInspectionScheduleRequest;
use App\Http\Requests\UpdateInspectionScheduleRequest;
use App\Models\InspectionSchedule;
use Illuminate\Http\Request;
use App\Models\AdoptionApplication;

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

    $inspectionSchedule = InspectionSchedule::create($requestData);

    if($inspectionSchedule->application_id){
      $inspectionSchedule->adoptionApplication()->update(['status' => 'under_review']);
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

    if($request->status === 'done'){
      $inspectionSchedule->update($requestData);

      return redirect()->back()->with('success','Inspection schedule has been marked done.');
    }

    if($request->status === 'cancelled'){
      $inspectionSchedule->update($requestData);

      return redirect()->back()->with('warning','Inspection schedule has been cancelled.');
    }
  }

  /**
    * Remove the specified resource from storage.
  */
  public function destroy(InspectionSchedule $inspectionSchedule)
  {
    //
  }
}
