@extends('layouts.app')

@section('content')
  <div class="card mt-0 mt-md-4 mb-4 mb-md-2 border-0 me-2 me-md-5 ms-2 ms-md-5 px-1 px-md-5">
    <div class="card-body border-0 p-2 p-md-5">
      <div class="card-header border-0 bg-secondary mb-md-3">
        <h3 class="text-center fw-bolder display-6 font-monospace mb-2 mb-md-0 mt-3 mt-md-0">Lost-and-Found Reports</h3>
        <div class="row g-3 g-md-5 mb-md-2 mb-1 justify-content-end mt-md-0">
          <div class="col-12 col-md-6 d-flex flex-column flex-md-row">
            <fieldset class="p-1 mt-0 mb-0">
              <legend class="fs-6 fw-bold mx-2 font-monospace" id="filter-legend">Filter by</legend>
              <div class="row g-2 mt-0">
                <div class="col-6">
                  <select class="form-select" aria-label="filter-select" aria-labelledby="filter-legend">
                    <option selected hidden disabled>Type</option>
                    <option>Lost Reports</option>
                    <option>Found Reports</option>
                  </select>
                </div>
                <div class="col-6">
                  <select class="form-select" aria-label="filter-select" aria-labelledby="filter-legend">
                    <option selected hidden disabled>Status</option>
                    <option>Active</option>
                    <option>Resolved</option>
                  </select>
                </div>
              </div>
            </fieldset>
            <fieldset class="ms-md-3 p-1 mt-0 mb-0">
              <legend class="fs-6 fw-bold mx-2" id="sort-legend">Sort by</legend>
              <div class="row g-2 mt-0">
                <div class="col-12">
                  <select class="form-select" aria-label="filter-select" aria-labelledby="filter-legend">
                    <option selected hidden disabled>Report Date</option>
                    <option>Newest</option>
                    <option>Oldest</option>
                  </select>
                </div>
              </div>
            </fieldset>
          </div>
          <div class="col-12 col-md-6 mt-3 mt-md-auto mt-0 d-flex flex-column justify-content-end" data-controller="report-switch">
            <div class="form-check form-switch align-self-start align-self-md-end mb-1 mb-md-3 me-md-1 ms-2 ms-md-auto">
              <input class="form-check-input " type="checkbox" value="" id="reportSwitch" switch data-report-switch-target="switch" data-action="report-switch#toggleFields">
              <label class="form-check-label mb-1 mb-md-0 ms-1 fw-bold font-monospace" for="reportSwitch" id="switchLabel">Switch to file a report!</label>
            </div>
            <!-- Search input for larger screens -->
            <div class="input-group w-50 h-50 d-none d-md-flex mt-auto mb-1 align-self-end">
              <input type="text" name="reportsSearchField" aria-label="Search" placeholder="Search Reports" class="form-control" data-report-switch-target="searchField">
            </div>
            <div class="d-flex justify-content-md-end justify-content-start">
              <div class="btn-group">
                <button type="button" class="btn btn-primary btn-lg fw-bold align-self-md-end align-self-start mt-auto mb-1 d-none dropdown-toggle" id="createReportButton" data-report-switch-target="createButton" data-bs-toggle="dropdown" aria-expanded="false">File a Report!</button>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#createLostAnimalReportModal">Lost Animal Report</a></li>
                  <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#createFoundAnimalReportModal">Found Animal Report</a></li>
                </ul>
              </div>
            </div>
            <!-- Search input for smaller screens -->
            <div class="input-group w-100 d-flex d-md-none px-1">
              <input type="text" name="reportsSearchField" aria-label="Search" placeholder="Search Reports" class="form-control" data-report-switch-target="searchField">
            </div>
          </div>
          @include('modals.reports.create-lost-animal-report-modal')
          @include('modals.reports.create-found-animal-report-modal')
        </div>
      </div>
      <div class="container-fluid mx-auto shadow-lg p-3 mb-5 rounded-4">
        @if ($reports->isEmpty())
          <div class="d-flex flex-column align-items-center justify-content-center my-5">
            <i class="bi bi-exclamation-circle fs-1 text-muted mb-2"></i>
            <p class="fs-4 fw-semibold text-muted">No reports yet.</p>
            <a href="" class="btn btn-primary mt-2 fw-semibold">Create your first report</a>
          </div>
        @else
          <div class="g-4 row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 justify-content-center" data-controller="view-report-modal delete-modal update-report-modal">
            @foreach ($reports as $report )
              <div class="col-12 col-md-3 rounded-4 border-primary-subtle bg-warning-subtle mx-2 px-1 mt-4 mt-md-5" data-aos="zoom-in-up" data-aos-delay="200">
                <div class="card border-0 bg-warning-subtle h-100">
                  <div class="card-header bg-warning-subtle border-0 d-flex justify-content-center mt-2">
                    <span class="text-dark text-uppercase fs-4 ms-2 p-2 fw-lighter">{{ $report->type_formatted }}</span>
                  </div>
                  <div class="card-body d-flex flex-column">
                    <div class="ratio ratio-4x3 p-0 p-md-2 mt-0 rescue-card">
                      <img src="{{ asset($report->image_url) }}" alt="Gallery image" class="w-100 h-100 object-fit-cover rounded-4">
                    </div>
                    <div class="d-flex flex-column mt-md-3 mt-2">
                      @if($report->isLostReport())
                        <span class="ms-3 mt-3"><strong>Name: </strong>{{ $report->animal_name_formatted }}</span>
                        <span class="ms-3 mt-3"><strong>Last Seen at: </strong>{{ $report->foundLastSeenLocation() }}</span>
                        <span class="ms-3 mt-3"><strong>Last Seen on: </strong>{{ $report->foundLastSeenDate() }}</span>
                      @else
                        <span class="ms-3 mt-3"><strong>Found at: </strong>{{ $report->foundLastSeenLocation() }}</span> 
                        <span class="ms-3 mt-3"><strong>Found on: </strong>{{ $report->foundLastSeenDate() }}</span>
                      @endif
                      <span class="ms-3 mt-3"><strong>Sex: </strong>{{ $report->sex_formatted }}</span>
                      <span class="ms-3 mt-3"><strong>Date Reported:</strong> {{ $report->reportedDate()}}</span>
                      <span class="ms-3 mt-3"><strong>Status:</strong> {{ $report->statusLabel()}}</span>
                    </div>
                  </div>
                  <div class="card-footer bg-warning-subtle border-0 d-flex gap-2 justify-content-between px-3 mt-auto mx-auto mb-2">
                    @if($report->isStillActive())
                      <a data-bs-toggle="modal" data-bs-target="#viewReportModal" class="btn btn-light"
                        data-report-id="{{ $report->id}}"
                        data-report-type="{{ $report->type}}" 
                        data-report-animal-name ="{{ $report->animal_name_formatted }}"
                        data-report-species="{{ $report->species }}"
                        data-report-location="{{ $report->foundLastSeenLocation() }}"
                        data-report-seen-date="{{ $report->foundLastSeenDate() }}"
                        data-report-breed="{{ $report->breed_formatted }}"
                        data-report-color="{{ $report->color_formatted }}"
                        data-report-sex="{{ $report->sex_formatted }}"
                        data-report-age-estimate="{{ $report->age_estimate_formatted }}"
                        data-report-size="{{ $report->size_formatted }}"
                        data-report-distinctive-features="{{ $report->distinctiveFeatures() }}"
                        data-report-condition="{{ $report->condition_formatted }}"
                        data-report-temporary-shelter="{{ $report->temporary_shelter_formatted }}"
                        data-report-owner-name="{{ $report->ownerFullName() }}"
                        data-report-owner-contact-number="{{ $report->getContactNumber() }}"
                        data-report-owner-email="{{ $report->getEmail() }}"
                        data-report-status="{{ $report->status }}"
                        data-report-status-label="{{ $report->statusLabel() }}"
                        data-report-owned-by-logged-user="{{ $report->ownedByLoggedUser() ? 'true' : 'false'}}"
                        data-report-logged-user-is-adminstaff="{{ $report->loggedUserIsAdminOrStaff() ? 'true' : 'false'}}">View Report
                      </a>
                      @if ($report->ownedByLoggedUser())
                        <a href="#" class="btn btn-primary fw-bolder"
                          data-report-id="{{ $report->id}}"
                          data-report-type="{{ $report->type}}"
                          data-report-status="{{ $report->status}}"
                          data-report-animal-name ="{{ $report->animal_name_formatted }}"
                          data-report-species="{{ $report->species }}"
                          data-report-location="{{ $report->foundLastSeenLocation() }}"
                          data-report-last-seen-date="{{ $report->last_seen_date }}"
                          data-report-found-date="{{ $report->found_date }}"
                          data-report-breed="{{ $report->breed_formatted }}"
                          data-report-color="{{ $report->color_formatted }}"
                          data-report-sex="{{ $report->sex }}"
                          data-report-age-estimate="{{ $report->age_estimate_formatted }}"
                          data-report-size="{{ $report->size }}"
                          data-report-distinctive-features="{{ $report->distinctiveFeatures() }}"
                          data-report-condition="{{ $report->condition_formatted }}"
                          data-report-temporary-shelter="{{ $report->temporary_shelter_formatted }}"
                          data-report-image="{{ $report->image_url }}"
                          data-report-type-formatted="{{ $report->type_formatted }}"
                          data-bs-toggle="modal"
                          data-bs-target="#updateReportModal">Update Report</a>
                      @else
                        <a href="#" class="btn btn-info fw-bolder">Alert Owner</a> 
                      @endif
                    @else
                      <a data-bs-toggle="modal" data-bs-target="#viewReportModal" class="btn btn-light"
                        data-report-id="{{ $report->id}}"
                        data-report-type="{{ $report->type}}"
                        data-report-animal-name ="{{ $report->animal_name_formatted }}"
                        data-report-species="{{ $report->species }}"
                        data-report-location="{{ $report->foundLastSeenLocation() }}"
                        data-report-seen-date="{{ $report->foundLastSeenDate() }}"
                        data-report-breed="{{ $report->breed_formatted }}"
                        data-report-color="{{ $report->color_formatted }}"
                        data-report-sex="{{ $report->sex_formatted }}"
                        data-report-age-estimate="{{ $report->age_estimate_formatted }}"
                        data-report-size="{{ $report->size_formatted }}"
                        data-report-distinctive-features="{{ $report->distinctiveFeatures() }}"
                        data-report-condition="{{ $report->condition_formatted }}"
                        data-report-temporary-shelter="{{ $report->temporary_shelter_formatted }}"
                        data-report-owner-name="{{ $report->ownerFullName() }}"
                        data-report-status="{{ $report->status }}"
                        data-report-status-label="{{ $report->statusLabel() }}"
                        data-report-owned-by-logged-user="{{ $report->ownedByLoggedUser() ? 'true' : 'false'}}"
                        data-report-logged-user-is-adminstaff="{{ $report->loggedUserIsAdminOrStaff() ? 'true' : 'false'}}">View Report
                      </a>
                      @if ($report->ownedByLoggedUser())
                        <a href="#" class="btn btn-primary fw-bolder"
                          data-report-id="{{ $report->id}}"
                          data-report-type="{{ $report->type}}"
                          data-report-status="{{ $report->status}}"
                          data-report-animal-name ="{{ $report->animal_name_formatted }}"
                          data-report-species="{{ $report->species }}"
                          data-report-location="{{ $report->foundLastSeenLocation() }}"
                          data-report-last-seen-date="{{ $report->last_seen_date }}"
                          data-report-found-date="{{ $report->found_date }}"
                          data-report-breed="{{ $report->breed_formatted }}"
                          data-report-color="{{ $report->color_formatted }}"
                          data-report-sex="{{ $report->sex }}"
                          data-report-age-estimate="{{ $report->age_estimate_formatted }}"
                          data-report-size="{{ $report->size }}"
                          data-report-distinctive-features="{{ $report->distinctiveFeatures() }}"
                          data-report-condition="{{ $report->condition_formatted }}"
                          data-report-temporary-shelter="{{ $report->temporary_shelter_formatted }}"
                          data-report-image="{{ $report->image_url }}"
                          data-report-type-formatted="{{ $report->type_formatted }}"
                          data-bs-toggle="modal"
                          data-bs-target="#updateReportModal">Update Report</a>
                      @endif
                    @endif
                  </div>
                </div>
              </div>
              @include('modals.reports.view_report_modal')
              @include('modals.reports.delete-report-modal')
              @include('modals.reports.update-report-modal')
            @endforeach
          </div>
          <div class="d-flex justify-content-end mt-4 mt-md-5">
            <div class="btn-group" role="group" aria-label="Basic example">
              <button type="button" class="btn btn-info"><span class="align-items-center"><i class="bi bi-chevron-double-left"></i> Prev</span></button>
              <button type="button" class="btn btn-info"><span class="align-items-center">Next <i class="bi bi-chevron-double-right"></i></span></button>
            </div>
          </div>
        @endif
      </div>
    </div>
  </div>
@endsection