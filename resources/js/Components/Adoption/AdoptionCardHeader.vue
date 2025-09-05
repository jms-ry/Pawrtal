<template>
  <div class="card-header border-0 bg-secondary">
    <h3 class="text-center fw-bolder display-6 font-monospace mb-3 mb-md-5 mt-3">Adopt a Rescue Today!</h3>
    <div class="row g-3 g-md-5 mb-md-3 mb-3 justify-content-end">
      <div class="col-12 col-md-6">
        <fieldset class="p-1">
          <legend class="fs-6 fw-bold mx-2 font-monospace" id="filter-legend">Filter by</legend>
          <div class="row g-2 mt-0">
            <div class="col-6 col-md-4">
              <select class="form-select" aria-label="filter-select" aria-labelledby="filter-legend">
                <option selected hidden disabled>Sex</option>
                <option>Male</option>
                <option>Female</option>
              </select>
            </div>
            <div class="col-6 col-md-4">
              <select class="form-select" aria-label="filter-select" aria-labelledby="filter-legend">
                <option selected hidden disabled>Size</option>
                <option>Small</option>
                <option>Medium</option>
                <option>Large</option>
              </select>
            </div>
          </div>
        </fieldset>
      </div>
      <div class="col-12 col-md-6 mt-3 mt-md-auto d-flex flex-column justify-content-end" data-controller="switch-search-button">
        <div class="form-check form-switch align-self-start align-self-md-end mb-2 mb-md-3 me-md-1 ms-2 ms-md-auto ">
          <input class="form-check-input " type="checkbox" value="" id="rescueSwitch" switch data-switch-search-button-target="switch" data-action="switch-search-button#toggleFields">
          <label class="form-check-label mb-1 mb-md-0 ms-1 fw-bold font-monospace" for="rescueSwitch" id="switchLabel">Switch to AI recommendation?</label>
        </div>
        <!-- Search input for larger screens -->
        <div class="input-group w-50 h-50 d-none d-md-flex mt-auto mb-1 align-self-end" id="rescueSearchField" >
          <input type="text" name="rescueSearchField" aria-label="Search" placeholder="Search..." class="form-control w-100" data-switch-search-button-target="searchField">
        </div>
        <div class="d-flex justify-content-md-end justify-content-start">
          <button type="button" class="btn btn-primary fw-bold align-self-md-end align-self-start mt-auto mb-1 d-none" id="matchRescueButton" data-switch-search-button-target="matchButton">Match Me a Rescue!</button>
        </div>
        <!-- Search input for smaller screens -->
        <div class="input-group w-100 d-flex d-md-none px-1" id="rescueSearchField" >
          <input type="text" name="rescueSearchField" aria-label="Search" placeholder="Search..." class="form-control " data-switch-search-button-target="searchField">
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
</script>