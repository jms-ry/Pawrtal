<template>
  <Head title="My Reports"></Head>
  <AppLayout>
    <div class="container-fluid">
      <div class="card border-0 p-md-5">
        <MyReportsCardHeader
          :user="user"
          :filters="filters"
          @search="handleSearch"
          @filter="handleFilter"
          :previousUrl="previousUrl"
        />

        <MyReportsDisplay
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
  import MyReportsCardHeader from '../../Components/User/MyReports/MyReportsCardHeader.vue';
  import AppLayout from '../../Layouts/AppLayout.vue';
  import MyReportsDisplay from '../../Components/User/MyReports/MyReportsDisplay.vue';

  const props = defineProps({
    user: {
      type:Object
    },
    reports: Object,
    filters: {
      type: Object,
      default: () => ({})
    },
    previousUrl:String,
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
  
    router.get('/users/my-reports', params, {
      preserveState: true,
      preserveScroll: false,
      replace: true,
    });
  };
</script>