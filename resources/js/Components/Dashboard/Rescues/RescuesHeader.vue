<template>
  <div class="card-header border-0 bg-secondary">
    <h4 class="fs-5 mb-md-4 mb-4 mt-3 mt-md-0 me-5 align-items-start">
      <a :href="previousUrl" v-if="showBackNav" class="text-decoration-none font-monospace fw-bolder mb-md-0 mb-5 text-danger fs-4"><i class="bi bi-chevron-left"></i><span class="ms-0">Back </span></a>
    </h4>
    <h3 class="text-center fw-bolder display-6 font-monospace mb-4 mt-3">Manage Rescues</h3>

    <div v-if="hasActiveFilters" class="mb-3">
      <div class="d-flex flex-wrap gap-2 align-items-center">
        <span class="text-dark fw-bold">Active filters:</span>
        
        <span v-if="filters.search" class="badge bg-info text-dark d-flex flex-block align-items-center">
          Search: "{{ filters.search }}"
          <button @click="clearFilter('search')" class="btn-close btn-close-dark ms-1" aria-label="Clear search"></button>
        </span>
        
        <span v-if="filters.sex" class="badge bg-info text-dark d-flex flex-block align-items-center">
          Sex: {{ filters.sex }}
          <button @click="clearFilter('sex')" class="btn-close btn-close-dark ms-1" aria-label="Clear sex filter"></button>
        </span>
        
        <span v-if="filters.size" class="badge bg-info text-dark d-flex flex-block align-items-center">
          Size: {{ filters.size }}
          <button @click="clearFilter('size')" class="btn-close btn-close-dark ms-1" aria-label="Clear size filter"></button>
        </span>
        
        <span v-if="filters.status" class="badge bg-info text-dark d-flex flex-block align-items-center">
          Status: {{ getStatusLabel(filters.status) }}
          <button @click="clearFilter('status')" class="btn-close btn-close-dark ms-1" aria-label="Clear status filter"></button>
        </span>
        
        <button @click="clearAllFilters" class="btn btn-sm btn-outline-secondary ms-2">
          Clear All
        </button>
      </div>
    </div>

    <div class="row g-3 g-md-5 mb-md-2 mb-1 justify-content-end mt-md-0">
      <div class="col-12 col-md-6 d-flex flex-column flex-md-row">
        <fieldset class="p-1">
          <legend class="fs-6 fw-bold mx-2 font-monospace" id="filter-legend">Filter by</legend>
          <div class="row g-2 mt-0">
            <div class="col-12 col-md-4">
              <select v-model="selectedSex" @change="onFilterChange('sex', $event.target.value)" class="form-select" aria-label="filter-select" aria-labelledby="filter-legend">
                <option hidden selected value="">Sex</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
              </select>
            </div>
            <div class="col-12 col-md-4">
              <select v-model="selectedSize" 
                @change="onFilterChange('size', $event.target.value)" class="form-select" aria-label="filter-select" aria-labelledby="filter-legend">
                <option hidden selected value="">Size</option>
                <option value="small">Small</option>
                <option value="medium">Medium</option>
                <option value="large">Large</option>
              </select>
            </div>
            <div class="col-12 col-md-4">
              <select v-model="selectedStatus" @change="onFilterChange('status', $event.target.value)" class="form-select" aria-label="filter-select"aria-labelledby="filter-legend">
                <option hidden selected value="">Status</option>
                <option value="available">Available</option>
                <option value="unavailable">Unavailable</option>
                <option value="adopted">Adopted</option>
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
          <input 
            type="text" 
            v-model="searchInput" 
            @input="onSearchInput" 
            name="rescueSearchField" 
            aria-label="Search" 
            placeholder="Search Rescues" 
            class="form-control" 
            data-rescue-switch-target="searchField"
          >
          <button 
            v-if="searchInput" 
            @click="clearSearch" 
            class="btn btn-info" 
            type="button" 
            aria-label="Clear search"
          > 
            <i class="bi bi-x-lg fw-bolder fs-6"></i>
          </button>
        </div>
        
        <!-- Search input for smaller screens -->
        <div class="input-group w-100 d-flex d-md-none px-1">
          <input 
            type="text" 
            v-model="searchInput" 
            @input="onSearchInput" 
            name="rescueSearchField" 
            aria-label="Search" 
            placeholder="Search Rescues" 
            class="form-control" 
            data-rescue-switch-target="searchField"
          >
          <button 
            v-if="searchInput" 
            @click="clearSearch" 
            class="btn btn-info" 
            type="button" 
            aria-label="Clear search"
          > 
            <i class="bi bi-x-lg fw-bolder fs-6"></i>
          </button>
        </div>
      </div>  
    </div>
  </div>
</template>

<script setup>
  import CreateRescueProfileModal from '@/Components/Modals/Rescues/CreateRescueProfileModal.vue';
  import { ref, watch, onMounted, computed } from 'vue';

  const props = defineProps({
    previousUrl: String,
    showBackNav: Boolean,
    user: Object,
    filters: {
      type: Object,
      default: () => ({})
    }
  })

  const emit = defineEmits(['search', 'filter']);

  const searchInput = ref('');
  const selectedSex = ref('');
  const selectedSize = ref('');
  const selectedStatus = ref('');

  const hasActiveFilters = computed(() => {
    return !!(props.filters.search || props.filters.sex || props.filters.size || props.filters.status);
  });

  onMounted(() => {
    searchInput.value = props.filters.search || '';
    selectedSex.value = props.filters.sex || '';
    selectedSize.value = props.filters.size || '';
    selectedStatus.value = props.filters.status || '';
  });

  watch(() => props.filters, (newFilters) => {
    searchInput.value = newFilters.search || '';
    selectedSex.value = newFilters.sex || '';
    selectedSize.value = newFilters.size || '';
    selectedStatus.value = newFilters.status || '';
  }, { deep: true });

  const onSearchInput = () => {
    emit('search', searchInput.value);
  };

  const onFilterChange = (filterType, value) => {
    const filterData = { ...props.filters };
  
    if (value) {
      filterData[filterType] = value;
    } else {
      delete filterData[filterType];
    }
  
    emit('filter', filterData);
  };

  const clearSearch = () => {
    searchInput.value = '';
    emit('search', '');
  };

  const clearFilter = (filterType) => {
    const filterData = { ...props.filters };
    delete filterData[filterType];
  
    if (filterType === 'search') {
      searchInput.value = '';
    } else if (filterType === 'sex') {
      selectedSex.value = '';
    } else if (filterType === 'size') {
      selectedSize.value = '';
    } else if (filterType === 'status') {
      selectedStatus.value = '';
    }
  
  emit('filter', filterData);
};

  const clearAllFilters = () => {
    searchInput.value = '';
    selectedSex.value = '';
    selectedSize.value = '';
    selectedStatus.value = '';
  
    emit('filter', {});
  };

  const getStatusLabel = (status) => {
    const labels = {
      'available': 'Available',
      'unavailable': 'Unavailable', 
      'adopted': 'Adopted'
    };
    return labels[status] || status;
  };
</script>