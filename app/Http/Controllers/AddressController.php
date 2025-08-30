<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
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
   public function store(StoreAddressRequest $request)
  {
    $requestData = $request->all();

    $address = Address::create($requestData);

    return redirect()->back()->with('success', 'Address created and assigned successfully!');
  }

  /**
    * Display the specified resource.
  */
  public function show(Address $address)
  {
    //
  }

  /**
    * Show the form for editing the specified resource.
  */
  public function edit(Address $address)
  {
    //
  }

  /**
    * Update the specified resource in storage.
  */
  public function update(UpdateAddressRequest $request, Address $address)
  {
    $requestData = $request->all();
    $address->update($requestData);

    return redirect()->back()->with('success', 'Address updated successfully!');
  }

  /**
    * Remove the specified resource from storage.
  */
  public function destroy(Address $address)
  {
    $address->delete();

    return redirect()->back()->with('success', 'Associated address was deleted successfully!');
  }
}
