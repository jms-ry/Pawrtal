<template>
  <Head title="Rescues"></Head>
  <AppLayout>
    <div class="card mt-2 mt-md-3 mb-4 mb-md-2 border-0 me-2 me-md-5 ms-2 ms-md-5 px-1 px-md-5">
      <div class="card-body border-0 p-2 p-md-5">
        <RescuesCardHeader
          :user="user"
          :filters="filters"
          @search="handleSearch"
          @filter="handleFilter"
        />
        <RescuesDisplay
          :user="user" 
          :rescues="rescues"
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
  import RescuesCardHeader from '@/Components/Rescues/RescuesCardHeader.vue';
  import RescuesDisplay from '@/Components/Rescues/RescuesDisplay.vue';
  import AppLayout from '../../Layouts/AppLayout.vue';

  const props = defineProps({
    user: {
      type: Object,
    },
    rescues: Object,
    filters: {
      type: Object,
      default: () => ({})
    }
  });

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
  
    router.get('/rescues', params, {
      preserveState: true,
      preserveScroll: false,
      replace: true,
    });
  };
</script>