<template>
  <Head title="Dashboard/Rescues"></Head>
  <AppLayout>
    <div class="container-fluid">
       <div class="card border-0 p-md-5">
        <RescuesHeader
          :previousUrl="previousUrl"
          :showBackNav="showBackNav"
          :user="user"
          :filters="filters"
          @search="handleSearch"
          @filter="handleFilter"
        />

        <RescuesDisplay 
          :rescues = "rescues"
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
  import RescuesHeader from '../../Components/Dashboard/Rescues/RescuesHeader.vue';
  import RescuesDisplay from '../../Components/Dashboard/Rescues/RescuesDisplay.vue';

  const props = defineProps({
    rescues: {
      type: Object,
      required: true
    },
    previousUrl: String,
    showBackNav: Boolean,
    user: Object,
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

    if (newFilters.sex) {
      params.sex = newFilters.sex;
    }

    if (newFilters.size) {
      params.size = newFilters.size;
    }

    if (newFilters.status) {
      params.status = newFilters.status;
    }
  
    router.get('/dashboard/rescues', params, {
      preserveState: true,
      preserveScroll: false,
      replace: true,
    });
  };
</script>