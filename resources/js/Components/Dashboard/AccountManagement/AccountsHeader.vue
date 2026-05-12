<template>
  <div class="card-header border-0 bg-secondary">
    <h4 class="fs-5 mb-md-4 mb-4 mt-3 mt-md-0 me-5 align-items-start">
      <a :href="previousUrl" v-if="showBackNav" class="text-decoration-none font-monospace fw-bolder mb-md-0 mb-5 text-danger fs-4"><i class="bi bi-chevron-left"></i><span class="ms-0">Back </span></a>
    </h4>
    <h3 class="text-center fw-bolder display-6 font-monospace mb-4 mt-3">Manage Staff Accounts</h3>

    <!-- Active Filters Display -->
    <div v-if="hasActiveFilters" class="mb-3">
      <div class="d-flex flex-wrap gap-2 align-items-center">
        <span class="text-dark fw-bold">Active filters:</span>
        
        <span v-if="filters.search" class="badge bg-info text-dark d-flex flex-block align-items-center">
          Search: "{{ filters.search }}"
          <button @click="clearFilter('search')" class="btn-close btn-close-dark ms-1" aria-label="Clear search"></button>
        </span>
        
        <span v-if="filters.type" class="badge bg-info text-dark d-flex flex-block align-items-center">
          Type: {{ filters.first_name }}
          <button @click="clearFilter('type')" class="btn-close btn-close-dark ms-1" aria-label="Clear size filter"></button>
        </span>
        
        <span v-if="filters.status" class="badge bg-info text-dark d-flex flex-block align-items-center">
          Status: {{ getStatusLabel(filters.last_name) }}
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

      <div class="col-12 col-md-6 mt-5 d-flex flex-column justify-content-end">
        <div class="d-flex justify-content-end">
          <button type="button" class="btn btn-primary fw-bold align-self-end mt-auto mb-1" id="createRescueProfileButton"  data-bs-toggle="modal" data-bs-target="#createStaffAccountModal">Create Staff Account</button>
        </div>
      </div>
    </div>
  </div>
  <CreateStaffAccountModal/>
</template>

<script setup>
  import { ref, watch, onMounted, computed } from 'vue';
  import CreateStaffAccountModal from '../../Modals/CreateStaffAccountModal.vue';

  const props = defineProps({
    previousUrl: String,
    showBackNav: Boolean,
    filters: {
      type: Object,
      default: () => ({})
    }
  })

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