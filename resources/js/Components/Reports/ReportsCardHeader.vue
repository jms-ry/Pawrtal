<template>
  <div class="card-header border-0 bg-secondary mb-md-3">
    <h3 class="text-center fw-bolder display-6 font-monospace mb-2 mb-md-5 mt-3 mt-md-0">Lost-and-Found Reports</h3>

    <!-- Active Filters Display -->
    <div v-if="hasActiveFilters" class="mb-3">
      <div class="d-flex flex-wrap gap-2 align-items-center">
        <span class="text-dark fw-bold">Active filters:</span>
        
        <span v-if="filters.search" class="badge bg-info text-dark d-flex flex-block align-items-center">
          Search: "{{ filters.search }}"
          <button @click="clearFilter('search')" class="btn-close btn-close-dark ms-1" aria-label="Clear search"></button>
        </span>
        
        <span v-if="filters.type" class="badge bg-info text-dark d-flex flex-block align-items-center">
          Type: {{ filters.type }}
          <button @click="clearFilter('type')" class="btn-close btn-close-dark ms-1" aria-label="Clear size filter"></button>
        </span>
        
        <span v-if="filters.status" class="badge bg-info text-dark d-flex flex-block align-items-center">
          Status: {{ getStatusLabel(filters.status) }}
          <button @click="clearFilter('status')" class="btn-close btn-close-dark ms-1" aria-label="Clear status filter"></button>
        </span>

        <span v-if="filters.sort" class="badge bg-info text-dark d-flex flex-block align-items-center">
          Sort By: {{ getSortLabel(filters.sort) }}
          <button @click="clearFilter('sort')" class="btn-close btn-close-dark ms-1" aria-label="Clear sort filter"></button>
        </span>
        
        <button @click="clearAllFilters" class="btn btn-sm btn-outline-secondary ms-2">
          Clear All
        </button>
      </div>
    </div>

    <div class="row g-3 g-md-5 mb-md-2 mb-1 justify-content-end mt-md-0">
      <div class="col-12 col-md-6 d-flex flex-column flex-md-row">
        <fieldset class="p-1 mt-0 mb-0">
          <legend class="fs-6 fw-bold mx-2 font-monospace" id="filter-legend">Filter by</legend>
          <div class="row g-2 mt-0">
            <div class="col-6">
              <select v-model="selectedType" @change="onFilterChange('type', $event.target.value)" class="form-select" aria-label="filter-select" aria-labelledby="filter-legend">
                <option selected hidden value="">Type</option>
                <option value="lost">Lost Reports</option>
                <option value="found">Found Reports</option>
              </select>
            </div>
            <div class="col-6">
              <select v-model="selectedStatus" @change="onFilterChange('status', $event.target.value)" class="form-select" aria-label="filter-select" aria-labelledby="filter-legend">
                <option selected hidden value="">Status</option>
                <option value="active">Active</option>
                <option value="resolved">Resolved</option>
              </select>
            </div>
          </div>
        </fieldset>
        <fieldset class="ms-md-3 p-1 mt-0 mb-0">
          <legend class="fs-6 fw-bold mx-2" id="sort-legend">Sort by</legend>
          <div class="row g-2 mt-0">
            <div class="col-12">
              <select v-model="selectedSort" @change="onSortChange($event.target.value)" class="form-select" aria-label="filter-select" aria-labelledby="filter-legend">
                <option selected hidden value="">Report Date</option>
                <option value="desc">Newest</option>
                <option value="asc">Oldest</option>
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
        <div class="d-flex justify-content-md-end justify-content-start">
          <div class="btn-group">
            <button type="button" class="btn btn-primary btn-lg fw-bold align-self-md-end align-self-start mt-auto mb-1 d-none dropdown-toggle" id="createReportButton" data-report-switch-target="createButton" data-bs-toggle="dropdown" aria-expanded="false">File a Report!</button>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" data-bs-toggle="modal" :data-bs-target="props.lostModal">Lost Animal Report</a></li>
              <li><a class="dropdown-item" data-bs-toggle="modal" :data-bs-target="props.foundModal">Found Animal Report</a></li>
            </ul>
          </div>
        </div>
        <!-- Search input for larger screens -->
        <div class="input-group w-50 h-50 d-none d-md-flex mt-auto mb-1 align-self-end">
          <input type="text" v-model="searchInput" @input="onSearchInput" name="reportsSearchField" aria-label="Search" placeholder="Search Reports" class="form-control" data-report-switch-target="searchField">
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
          <input type="text" v-model="searchInput" @input="onSearchInput" name="reportsSearchField" aria-label="Search" placeholder="Search Reports" class="form-control" data-report-switch-target="searchField">
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
  import { ref, watch, onMounted, computed } from 'vue';
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
    },
    filters: {
      type: Object,
      default: () => ({})
    }
  });

  const emit = defineEmits(['search', 'filter']);

  const searchInput = ref('');
  const selectedType = ref('');
  const selectedStatus = ref('');
  const selectedSort = ref('');

  const hasActiveFilters = computed(() => {
    return !!(props.filters.search || props.filters.type || props.filters.status || props.filters.sort);
  });

  onMounted(() => {
    searchInput.value = props.filters.search || '';
    selectedType.value = props.filters.type || '';
    selectedStatus.value = props.filters.status || '';
    selectedSort.value = props.filters.sort || '';
  });

  watch(() => props.filters, (newFilters) => {
    searchInput.value = newFilters.search || '';
    selectedType.value = newFilters.type || '';
    selectedStatus.value = newFilters.status || '';
    selectedSort.value = newFilters.sort || '';
  }, { deep: true });

  const onSortChange = (value) => {
    const filterData = { ...props.filters };
    if (value) {
      filterData.sort = value;
    } else {
      delete filterData.sort;
    }
    emit('filter', filterData);
  };

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
    } else if (filterType === 'type') {
      selectedType.value = '';
    } else if (filterType === 'status') {
      selectedStatus.value = '';
    }else if (filterType === 'sort') {
      selectedSort.value = '';
    }
  
  emit('filter', filterData);
};

  const clearAllFilters = () => {
    searchInput.value = '';
    selectedType.value = '';
    selectedStatus.value = '';
    selectedSort.value = '';
  
    emit('filter', {});
  };

  const getStatusLabel = (status) => {
    const labels = {
      'active': 'Active',
      'resolved': 'Resolved'
    };
    return labels[status] || status;
  };

  const getSortLabel = (sort) => {
    const labels = {
      'desc' : 'Newest',
      'asc': 'Oldest'
    };

    return labels[sort] || sort;
  }
</script>