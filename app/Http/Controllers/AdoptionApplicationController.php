<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\AdoptionApplication;

class AdoptionApplicationController extends Controller
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
  public function create(Request $request)
  {

  }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
      $requestData = $request->all();

      if($request->hasFile('valid_id')){
        $validIdPath = $request->file('valid_id')->store('images/adoption_applications/valid_ids','public');

        $requestData['valid_id'] = $validIdPath;
      }

      if($request->hasFile('supporting_documents')){
        $supporting_documents = [];

        foreach ($request->file('supporting_documents') as $supporting_document){
          $supportingDocumentPath = $supporting_document->store('images/adoption_applications/supporting_documents','public');
          $supporting_documents [] = $supportingDocumentPath;
        }
        $requestData['supporting_documents'] = $supporting_documents;
      }else{
        $requestData['supporting_documents'] = [];
      }

      $adoption_application = AdoptionApplication::create($requestData);

      return redirect()->route('users.myAdoptionApplications')->with('success', 'Adoption application for '. $adoption_application->rescue->name. ' was submitted!');
    }

    /**
     * Display the specified resource.
     */
    public function show(AdoptionApplication $adoptionApplication)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AdoptionApplication $adoptionApplication)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AdoptionApplication $adoptionApplication)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AdoptionApplication $adoptionApplication)
    {
        //
    }
}
