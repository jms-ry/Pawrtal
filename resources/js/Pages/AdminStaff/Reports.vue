<template>
  <Head title="Dashboard/Reports"></Head>
  <AppLayout>
    <div class="container-fluid">
       <div class="card border-0 p-md-5">

        <ReportsHeader 
          :previousUrl="previousUrl"
          :showBackNav="showBackNav"
          :filters="filters"
          @search="handleSearch"
          @filter="handleFilter"
        />

        <ReportsDisplay 
          :reports = "reports"
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
  import AppLayout from '../../Layouts/AppLayout.vue';
  import ReportsHeader from '../../Components/Dashboard/Reports/ReportsHeader.vue';
  import ReportsDisplay from '../../Components/Dashboard/Reports/ReportsDisplay.vue';
  const props = defineProps({
    reports: {
      type: Object,
      required: true
    },
    previousUrl: String,
    showBackNav: Boolean,
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
  
    router.get('/dashboard/reports', params, {
      preserveState: true,
      preserveScroll: false,
      replace: true,
    });
  };
</script>