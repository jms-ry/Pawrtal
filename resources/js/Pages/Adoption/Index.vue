<template>
  <Head title="Adoption"></Head>
  <AppLayout>
    <div class="card mt-2 mt-md-3 mb-4 mb-md-2 border-0 me-2 me-md-5 ms-2 ms-md-5 px-1 px-md-5">
      <div class="card-body border-0 p-2 p-md-5">
        <AdoptionCardHeader 
          :filters="filters"
          @search="handleSearch"
          @filter="handleFilter"
        />

        <AdoptionDisplay 
          :user = "user"
          :adoptables = "adoptables"
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
  import AdoptionCardHeader from '../../Components/Adoption/AdoptionCardHeader.vue';
  import AdoptionDisplay from '../../Components/Adoption/AdoptionDisplay.vue';
  import AppLayout from '../../Layouts/AppLayout.vue';

  const props = defineProps({
    adoptables: {
      type: Object,
      default: () => null
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
  
    router.get('/adoption', params, {
      preserveState: true,
      preserveScroll: false,
      replace: true,
    });
  };
</script>