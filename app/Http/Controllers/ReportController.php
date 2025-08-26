<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReportRequest;
use App\Http\Requests\UpdateReportRequest;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
class ReportController extends Controller
{
  /**
    * Display a listing of the resource.
  */
  public function index()
  {
    $user = Auth::user();
    $reports = Report::with('user')->orderBy('id','asc')->get();
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
  public function store(StoreReportRequest $request)
  {
    $requestData = $request->all();

    if ($request->hasFile('image')) {
      $imagePath = $request->file('image')->store('images/reports/reports_images', 'public');

      $requestData['image'] = $imagePath;
    }

    $report = Report::create($requestData);

    return redirect()->back()->with('success', $report->getTypeFormattedAttribute() . ' Report created successfully!');
  }

  /**
    * Display the specified resource.
  */
  public function show(Report $report)
  {
    //
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
  public function update(UpdateReportRequest $request, Report $report)
  {
    $requestData = $request->all();

    if ($request->hasFile('image')) {
      Storage::delete($report->image);

      $imagePath = $request->file('image')->store('images/reports/reports_images', 'public');

      $requestData['image'] = $imagePath;
    }

    $report->update($requestData);
    return redirect()->route('reports.index')->with('success', $report->getTypeFormattedAttribute() . ' Report updated successfully!');
  }

  /**
    * Remove the specified resource from storage.
  */
  public function destroy(Report $report)
  {
    $report->delete();

    return redirect()->route('reports.index')->with('success', $report->getTypeFormattedAttribute() . ' Report has been deleted successfully!');
  }
}
