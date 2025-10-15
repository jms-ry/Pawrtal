<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHouseholdRequest;
use App\Http\Requests\UpdateHouseholdRequest;
use App\Models\Household;
use Illuminate\Http\Request;

class HouseholdController extends Controller
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
  public function store(StoreHouseholdRequest $request)
  {
    $requestData = $request->all();

    $household = Household::create($requestData);

    return redirect()->back()->with('success', 'Household information has been created!');
  }

  /**
    * Display the specified resource.
  */
  public function show(Household $household)
  {
    //
  }

  /**
    * Show the form for editing the specified resource.
  */
  public function edit(Household $household)
  {
    //
  }

  /**
    * Update the specified resource in storage.
  */
  public function update(UpdateHouseholdRequest $request, Household $household)
  {
    $this->authorize('update',$household);

    $requestData = $request->all();
    $household->update($requestData);

    return redirect()->back()->with('info', 'Household information has been updated!');
  }

  /**
    * Remove the specified resource from storage.
  */
  public function destroy(Household $household)
  {
    $this->authorize('delete',$household);
    
    $household->delete();

    return redirect()->back()->with('warning', 'Household information has been deleted!');
  }
}
