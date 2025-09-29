<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInspectionScheduleRequest;
use App\Models\InspectionSchedule;
use Illuminate\Http\Request;

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
  public function update(Request $request, InspectionSchedule $inspectionSchedule)
  {
    //
  }

  /**
    * Remove the specified resource from storage.
  */
  public function destroy(InspectionSchedule $inspectionSchedule)
  {
    //
  }
}
