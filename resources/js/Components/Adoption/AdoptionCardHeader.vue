<template>
  <div class="card-header border-0 bg-secondary">
    <h3 class="text-center fw-bolder display-6 font-monospace mb-3 mb-md-5 mt-3">Adopt a Rescue Today!</h3>

    <!-- Active Filters Display -->
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
        
        <button @click="clearAllFilters" class="btn btn-sm btn-outline-secondary ms-2">
          Clear All
        </button>
      </div>
    </div>

    <div class="row g-3 g-md-5 mb-md-3 mb-3 justify-content-end">
      <div class="col-12 col-md-6">
        <fieldset class="p-1">
          <legend class="fs-6 fw-bold mx-2 font-monospace" id="filter-legend">Filter by</legend>
          <div class="row g-2 mt-0">
            <div class="col-6 col-md-4">
              <select v-model="selectedSex" @change="onFilterChange('sex', $event.target.value)" class="form-select" aria-label="filter-select" aria-labelledby="filter-legend">
                <option hidden selected value="">Sex</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
              </select>
            </div>
            <div class="col-6 col-md-4">
              <select v-model="selectedSize" 
                @change="onFilterChange('size', $event.target.value)" class="form-select" aria-label="filter-select" aria-labelledby="filter-legend">
                <option hidden selected value="">Size</option>
                <option value="small">Small</option>
                <option value="medium">Medium</option>
                <option value="large">Large</option>
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
        <div class="d-flex justify-content-md-end justify-content-start">
          <button type="button" class="btn btn-primary fw-bold align-self-md-end align-self-start mt-auto mb-1 d-none" id="matchRescueButton" data-switch-search-button-target="matchButton">Match Me a Rescue!</button>
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
            data-switch-search-button-target="searchField"
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
            data-rescue-switch-button-target="searchField"
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
  import { ref, watch, onMounted, computed } from 'vue';

  const props = defineProps({
    filters: {
      type: Object,
      default: () => ({})
    }
  });

  const emit = defineEmits(['search', 'filter']);

  const searchInput = ref('');
  const selectedSex = ref('');
  const selectedSize = ref('');

  const hasActiveFilters = computed(() => {
    return !!(props.filters.search || props.filters.sex || props.filters.size);
  });

  onMounted(() => {
    searchInput.value = props.filters.search || '';
    selectedSex.value = props.filters.sex || '';
    selectedSize.value = props.filters.size || '';
  });

  watch(() => props.filters, (newFilters) => {
    searchInput.value = newFilters.search || '';
    selectedSex.value = newFilters.sex || '';
    selectedSize.value = newFilters.size || '';
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
    }
  
  emit('filter', filterData);
};

  const clearAllFilters = () => {
    searchInput.value = '';
    selectedSex.value = '';
    selectedSize.value = '';
  
    emit('filter', {});
  };
</script>