<template>
  <div class="card-header border-0 bg-secondary">
    <h4 class="fs-5 mb-md-4 mb-4 mt-3 mt-md-0 me-5 align-items-start">
      <a :href="previousUrl" class="text-decoration-none font-monospace fw-bolder text-danger fs-4"><i class="bi bi-chevron-left"></i><span class="ms-0">Back </span></a>
    </h4>
    <h3 class="text-center fw-bolder display-6 font-monospace mb-4 mt-3">{{ user?.fullName }}'s Notifications!</h3>
    <!-- Active Filters Display -->
    <div v-if="hasActiveFilters" class="mb-3">
      <div class="d-flex flex-wrap gap-2 align-items-center">
        <span class="text-dark fw-bold">Active filters:</span>
        
        <span v-if="filters.search" class="badge bg-info text-dark d-flex flex-block align-items-center">
          Search: "{{ filters.search }}"
          <button @click="clearFilter('search')" class="btn-close btn-close-dark ms-1" aria-label="Clear search"></button>
        </span>
        
        <span v-if="filters.read_at" class="badge bg-info text-dark d-flex flex-block align-items-center">
          Type: {{ getReadAtLabel(filters.read_at) }}
          <button @click="clearFilter('read_at')" class="btn-close btn-close-dark ms-1" aria-label="Clear size filter"></button>
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
            <div class="col-12">
              <select v-model="selectedReadAt" @change="onFilterChange('read_at', $event.target.value)" class="form-select" aria-label="filter-select" aria-labelledby="filter-legend">
                <option selected hidden value="">Type</option>
                <option value="unread">Unread Notifications</option>
                <option value="read">Read Notifications</option>
              </select>
            </div>
          </div>
        </fieldset>
        <fieldset class="ms-md-3 p-1 mt-0 mb-0">
          <legend class="fs-6 fw-bold mx-2" id="sort-legend">Sort by</legend>
          <div class="row g-2 mt-0">
            <div class="col-12">
              <select v-model="selectedSort" @change="onSortChange($event.target.value)" class="form-select" aria-label="filter-select" aria-labelledby="filter-legend">
                <option selected hidden value="">Received Date</option>
                <option value="desc">Newest</option>
                <option value="asc">Oldest</option>
              </select>
            </div>
          </div>
        </fieldset>
      </div>

      <div class="col-12 col-md-6 mt-3 mt-md-auto mt-0 d-flex flex-column justify-content-end">
        <!-- Search input for larger screens -->
        <div class="input-group w-50 h-50 d-none d-md-flex mt-auto mb-1 align-self-end">
          <input type="text" v-model="searchInput" @input="onSearchInput" name="notificationsSearchField" aria-label="Search" placeholder="Search Notifications" class="form-control">
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
          <input type="text" v-model="searchInput" @input="onSearchInput" name="notificationsSearchField" aria-label="Search" placeholder="Search Notifications" class="form-control">
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
    user: {
      type:Object
    },
    filters: {
      type: Object,
      default: () => ({})
    },
    previousUrl:String,
  })

  const emit = defineEmits(['search', 'filter']);

  const searchInput = ref('');
  const selectedReadAt = ref('');
  const selectedSort = ref('');

  const hasActiveFilters = computed(() => {
    return !!(props.filters.search || props.filters.read_at || props.filters.sort);
  });

  onMounted(() => {
    searchInput.value = props.filters.search || '';
    selectedReadAt.value = props.filters.read_at || '';
    selectedSort.value = props.filters.sort || '';
  });

  watch(() => props.filters, (newFilters) => {
    searchInput.value = newFilters.search || '';
    selectedReadAt.value = newFilters.read_at || '';
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

  const onFilterChange = (filterReadAt, value) => {
    const filterData = { ...props.filters };
  
    if (value) {
      filterData[filterReadAt] = value;
    } else {
      delete filterData[filterReadAt];
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
    } else if (filterType === 'read_at') {
      selectedReadAt.value = '';
    } else if (filterType === 'sort') {
      selectedSort.value = '';
    }
  
  emit('filter', filterData);
};

  const clearAllFilters = () => {
    searchInput.value = '';
    selectedReadAt.value = '';
    selectedSort.value = '';
  
    emit('filter', {});
  };

  const getReadAtLabel = (read_at) => {
    const labels = {
      'read': 'Read Notifications',
      'unread': 'Unread Notifications'
    };
    return labels[read_at] || read_at;
  };

  const getSortLabel = (sort) => {
    const labels = {
      'desc' : 'Newest',
      'asc': 'Oldest'
    };

    return labels[sort] || sort;
  }
</script>