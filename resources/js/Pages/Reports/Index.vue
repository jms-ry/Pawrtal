<template>
  <Head title="Reports"></Head>
  <AppLayout>
    <div class="card mt-0 mt-md-4 mb-4 mb-md-2 border-0 me-2 me-md-5 ms-2 ms-md-5 px-1 px-md-5">
      <div class="card-body border-0 p-2 p-md-5">
        <ReportsCardHeader 
          :lostModal = "lostModal"
          :foundModal = "foundModal"
          :user="user"
          :filters="filters"
          @search="handleSearch"
          @filter="handleFilter"
        />
        <ReportsDisplay
          :reports = "reports"
          :user="user"
          :filters="filters"
        />
      </div>
    </div>
  </AppLayout>
  
</template>

<script setup>
  import { Head } from '@inertiajs/vue3';
  import { router } from '@inertiajs/vue3';
  import { ref } from 'vue';
  import ReportsCardHeader from '../../Components/Reports/ReportsCardHeader.vue';
  import ReportsDisplay from '../../Components/Reports/ReportsDisplay.vue';
  import AppLayout from '../../Layouts/AppLayout.vue';

  const props = defineProps({
    lostModal: String,
    foundModal: String,
    user: Object,
    reports: Object,
    filters: {
      type: Object,
      default: () => ({})
    }
  })

  let searchTimeout = ref(null);

  const handleSearch = (searchTerm) => {
    if (searchTimeout) {
      clearTimeout(searchTimeout);
    }

    searchTimeout = setTimeout(() => {
      const newFilters = { ...props.filters, search: searchTerm };
      applyFilters(newFilters);
    }, 500);
  };

  const handleFilter = (filterData) => {
    if (searchTimeout) {
      clearTimeout(searchTimeout);
    }
  
    applyFilters(filterData);
  };

  const applyFilters = (newFilters) => {
    const params = {};
  
    if (newFilters.search && newFilters.search.trim()) {
      params.search = newFilters.search.trim();
    }

    if (newFilters.type) {
      params.type = newFilters.type;
    }

    if (newFilters.status) {
      params.status = newFilters.status;
    }

    if (newFilters.sort) {
      params.sort = newFilters.sort;
    }
  
    router.get('/reports', params, {
      preserveState: true,
      preserveScroll: false,
      replace: true,
    });
  };
</script>