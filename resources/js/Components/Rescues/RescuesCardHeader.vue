<template>
  <div class="card-header border-0 bg-secondary">
    <h3 class="text-center fw-bolder display-6 font-monospace mb-4 mt-3">Meet Our Rescues!</h3>
    <div class="row g-3 g-md-5 mb-4 justify-content-end">
      <div class="col-12 col-md-6">
        <fieldset class="p-1">
          <legend class="fs-6 fw-bold mx-2 font-monospace" id="filter-legend">Filter by</legend>
          <div class="row g-2 mt-0">
            <div class="col-12 col-md-4">
              <select class="form-select" aria-label="filter-select" aria-labelledby="filter-legend">
                <option selected hidden>Sex</option>
                <option>Male</option>
                <option>Female</option>
              </select>
            </div>
            <div class="col-12 col-md-4">
              <select class="form-select" aria-label="filter-select" aria-labelledby="filter-legend">
                <option selected hidden>Size</option>
                <option>Small</option>
                <option>Medium</option>
                <option>Large</option>
              </select>
            </div>
            <div class="col-12 col-md-4">
              <select class="form-select" aria-label="filter-select" aria-labelledby="filter-legend">
                <option selected hidden>Adoption Status</option>
                <option>Available</option>
                <option>Unavailable</option>
                <option>Adopted</option>
              </select>
            </div>
          </div>
        </fieldset>
      </div>
      <div class="col-12 col-md-6 mt-3 mt-md-auto mt-0 d-flex flex-column justify-content-end" v-bind:data-controller="props.user?.isAdminOrStaff ? 'rescue-switch' : null">
        <CreateRescueProfileModal/>
        <div v-if="user?.isAdminOrStaff" class="form-check form-switch align-self-start align-self-md-end mb-1 mb-md-3 me-md-1 ms-2 ms-md-auto">
          <input class="form-check-input " type="checkbox" value="" id="rescueSwitch" switch data-rescue-switch-target="switch" data-action="rescue-switch#toggle">
          <label class="form-check-label mb-1 mb-md-0 ms-1 fw-bold font-monospace" for="rescueSwitch" id="switchLabel">Switch to create new rescue profile!</label>
        </div>
        <div class="d-flex justify-content-md-end justify-content-start">
          <button type="button" class="btn btn-primary fw-bold align-self-md-end align-self-start mt-auto mb-1 d-none" id="createRescueProfileButton" data-rescue-switch-target="createButton" data-bs-toggle="modal" data-bs-target="#createRescueProfileModal">Create Rescue Profile</button>
        </div>
        <!-- Search input for larger screens -->
        <div class="input-group w-50 h-50 d-none d-md-flex mt-auto mb-1 align-self-end">
          <input type="text" v-model="searchInput" @input="onSearchInput" name="rescueSearchField" aria-label="Search" placeholder="Search Rescues" class="form-control" data-rescue-switch-target="searchField">
          <!-- Loading indicator -->
          <div v-if="isSearching" class="position-absolute end-0 top-50 translate-middle-y me-2">
            <div class="spinner-border spinner-border-sm text-primary" role="status" aria-hidden="true"></div>
          </div>
          <!-- Clear button -->
          <button v-if="searchInput" @click="clearSearch" class="btn btn-info" type="button" aria-label="Clear search"> <i class="bi bi-x-lg fw-bolder fs-6"></i></button>
        </div>
        <!-- Search input for smaller screens -->
        <div class="input-group w-100 d-flex d-md-none px-1">
          <input type="text" v-model="searchInput" @input="onSearchInput" name="rescueSearchField" aria-label="Search" placeholder="Search Rescues" class="form-control" data-rescue-switch-target="searchField">
          <!-- Loading indicator -->
          <div v-if="isSearching" class="position-absolute end-0 top-50 translate-middle-y me-2">
            <div class="spinner-border spinner-border-sm text-primary" role="status" aria-hidden="true"></div>
          </div>
          <!-- Clear button -->
          <button v-if="searchInput" @click="clearSearch" class="btn btn-info" type="button" aria-label="Clear search"> <i class="bi bi-x-lg fw-bolder fs-6"></i></button>
        </div>
      </div>  
    </div>
  </div>
</template>
<script setup>
  import { ref, watch, onMounted } from 'vue';
  import CreateRescueProfileModal from '../Modals/Rescues/CreateRescueProfileModal.vue';
  const props = defineProps({
    user: {
      type: Object
    },
    search: {
      type: String,
      default: ''
    }
  });
  const emit = defineEmits(['search']);

  const searchInput = ref('');
  const isSearching = ref(false);

  onMounted(() => {
    searchInput.value = props.search || '';
  });

  watch(() => props.search, (newSearch) => {
    searchInput.value = newSearch || '';
    isSearching.value = false;
  });

  const onSearchInput = () => {
    isSearching.value = true;
    emit('search', searchInput.value);
  
    setTimeout(() => {
      isSearching.value = false;
    }, 500);
  };

  const clearSearch = () => {
    searchInput.value = '';
    isSearching.value = false;
    emit('search', '');
  };
</script>