<template>
  <Head title="My Donations"></Head>
  <AppLayout>
    <div class="container-fluid">
      <div class="card border-0 p-md-5">
        <MyDonationsCardHeader
          :user="user"
          :filters="filters"
          @search="handleSearch"
          @filter="handleFilter"
          :previousUrl="previousUrl"
        />

        <MyDonationsDisplay
          :donations = "donations"
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
  import MyDonationsCardHeader from '../../Components/User/MyDonations/MyDonationsCardHeader.vue';
  import AppLayout from '../../Layouts/AppLayout.vue';
import MyDonationsDisplay from '../../Components/User/MyDonations/MyDonationsDisplay.vue';

  const props = defineProps({
    user: {
      type:Object
    },
    donations: Object,
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

    if (newFilters.donation_type) {
      params.donation_type = newFilters.donation_type;
    }

    if (newFilters.status) {
      params.status = newFilters.status;
    }

    if (newFilters.sort) {
      params.sort = newFilters.sort;
    }
  
    router.get('/users/my-donations', params, {
      preserveState: true,
      preserveScroll: false,
      replace: true,
    });
  };
</script>