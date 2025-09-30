<template>
  <Head title="Dashboard/AdoptionApplications"></Head>

  <AppLayout>
    <div class="container-fluid">
      <div class="card border-0 p-md-5">
        <AdoptionApplicationsHeader
          :previousUrl="previousUrl"
          :showBackNav="showBackNav"
          :filters="filters"
          @search="handleSearch"
          @filter="handleFilter"
        />
        <AdoptionApplicationsDisplay
          :adoptionApplications="adoptionApplications"
          :filters="filters"
          :inspectors="inspectors"
          :user="user"
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
  import AdoptionApplicationsHeader from '../../Components/Dashboard/AdoptionApplications/AdoptionApplicationsHeader.vue';
  import AdoptionApplicationsDisplay from '../../Components/Dashboard/AdoptionApplications/AdoptionApplicationsDisplay.vue';

  const props = defineProps({
    user: {
      type:Object
    },
    adoptionApplications: Object,
    filters: {
      type: Object,
      default: () => ({})
    },
    previousUrl:String,
    showBackNav:Boolean,
    inspectors:Object,
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

    if (newFilters.status) {
      params.status = newFilters.status;
    }

    if (newFilters.sort) {
      params.sort = newFilters.sort;
    }
  
    router.get('/dashboard/adoption-applications', params, {
      preserveState: true,
      preserveScroll: false,
      replace: true,
    });
  };

</script>