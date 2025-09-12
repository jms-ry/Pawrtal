<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReportRequest;
use App\Http\Requests\UpdateReportRequest;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Http\Request;

class ReportController extends Controller
{
  /**
    * Display a listing of the resource.
  */
  public function index(Request $request)
  {
    $user = Auth::user();

    $search = $request->get('search');
    $typeFilter = $request->get('type');;
    $statusFilter = $request->get('status');
    $sortOrder = $request->get('sort');
    $sortOrder = in_array($sortOrder, ['asc','desc']) ? $sortOrder : null;
    $reports = Report::query()
      ->with('user')
      ->when($search, function ($query, $search) {
        $columns = ['animal_name','species', 'sex' ,'breed', 'color', 'type'];
        $keywords = preg_split('/[\s,]+/', $search, -1, PREG_SPLIT_NO_EMPTY);
        return $query->where(function ($q) use ($keywords, $columns) {
          foreach ($keywords as $word) {
            $q->where(function ($subQ) use ($word, $columns) {
              foreach ($columns as $col) {
                $subQ->orWhereRaw("LOWER($col) LIKE LOWER(?)", ['%' . $word . '%']);
              }
            });
          }
        });
      })
      ->when($typeFilter, function ($query, $typeFilter) {
        return $query->where('type', $typeFilter);
      })
      ->when($statusFilter, function ($query, $statusFilter) {
        return $query->where('status', $statusFilter);
      })
      ->when($sortOrder, function($query,$sortOrder){
        return $query->orderBy('created_at',$sortOrder);
      })
      ->orderBy('created_at', 'desc')
      ->paginate(9)
    ->withQueryString();
      
    $lostModal = Auth::check() ? '#createLostAnimalReportModal' : '#loginReminderModal';
    $foundModal = Auth::check() ? '#createFoundAnimalReportModal' : '#loginReminderModal';

    return Inertia::render('Reports/Index',[
      'lostModal' => $lostModal,
      'foundModal' => $foundModal,
      'user' => $user,
      'reports' => $reports,
      'filters' => [
        'search' => $search,
        'type' => $typeFilter,
        'status' => $statusFilter,
        'sort' => $sortOrder,
      ],
    ]);
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

    return redirect()->back()->with('success', $report->getTypeFormattedAttribute() . ' Report has been created!');
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
      if($report->image){
        Storage::delete($report->image);
      }
      $imagePath = $request->file('image')->store('images/reports/reports_images', 'public');
      $requestData['image'] = $imagePath;
    }else{
      unset($requestData['image']);
    }

    $report->update($requestData);
    
    return redirect()->route('reports.index')->with('info', $report->getTypeFormattedAttribute() . ' Report updated successfully!');
  }

  /**
    * Remove the specified resource from storage.
  */
  public function destroy(Report $report)
  {
    $report->delete();

    return redirect()->route('reports.index')->with('warning', $report->getTypeFormattedAttribute() . ' Report has been deleted successfully!');
  }
}
