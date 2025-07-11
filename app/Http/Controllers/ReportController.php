<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
  /**
    * Display a listing of the resource.
  */
  public function index()
  {
    $reports = Report::with('user')->get();
    return view('reports.index', compact('reports'));
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
    //
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
    //
  }
}
