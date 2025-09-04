<template>
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
              <li><a class="dropdown-item" data-bs-toggle="modal" :data-bs-target="props.lostModal">Lost Animal Report</a></li>
              <li><a class="dropdown-item" data-bs-toggle="modal" :data-bs-target="props.foundModal">Found Animal Report</a></li>
            </ul>
          </div>
        </div>
        <!-- Search input for smaller screens -->
        <div class="input-group w-100 d-flex d-md-none px-1">
          <input type="text" name="reportsSearchField" aria-label="Search" placeholder="Search Reports" class="form-control" data-report-switch-target="searchField">
         </div>
      </div>
      <LoginReminder />
      <CreateLostAnimalReportModal
        :user="user" 
      />
      <CreateFoundAnimalReportModal
        :user="user"
      />
    </div>
  </div>
</template>

<script setup>
  import LoginReminder from '@/Components/Modals/LoginReminder.vue';
  import CreateLostAnimalReportModal from '../Modals/Reports/CreateLostAnimalReportModal.vue';
  import CreateFoundAnimalReportModal from '../Modals/Reports/CreateFoundAnimalReportModal.vue';

  const props = defineProps({
    lostModal: {
      type: String,
      required: true
    },
    foundModal: {
      type: String,
      required: true
    },
    user: {
      type: Object,
      default: () => null
    }
  });
</script>