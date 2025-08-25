<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
  /**
    * Display a listing of the resource.
  */
  public function index()
  {
    $user = Auth::user();
    $reports = Report::with('user')->get();
    return view('reports.index', compact('reports','user'));
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
  public function store(Request $request)
  {
    $requestData = $request->all();

    if ($request->hasFile('image')) {
      $imagePath = $request->file('image')->store('images/reports/reports_images', 'public');

      $requestData['image'] = $imagePath;
    }

    $report = Report::create($requestData);

    return redirect()->back()->with('success', 'Report created successfully!');
  }

  /**
    * Display the specified resource.
  */
  public function show(Report $report)
  {
    return view('reports.show', compact('report'));
  }

  /**
    * Show the form for editing the specified resource.
  */
  public function edit(Report $report)
  {
    //
  }

  /**
    * Update the specified resource in storage.
  */
  public function update(Request $request, Report $report)
  {
    //
  }

  /**
    * Remove the specified resource from storage.
  */
  public function destroy(Report $report)
  {
    $report->delete();

    return redirect()->route('reports.index')->with('success', 'Report for has been deleted successfully!');
  }
}
