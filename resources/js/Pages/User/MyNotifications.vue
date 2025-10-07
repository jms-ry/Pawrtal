<template>
  <Head title="My Schedules"></Head>
  <AppLayout>
    <div class="container-fluid">
      <div class="card border-0 p-md-5">
        <MyNotificationsCardHeader
          :user="user"
          :previousUrl="previousUrl"
          :filters="filters"
          @search="handleSearch"
          @filter="handleFilter"
        />

        <MyNotificationsDisplay
          :notifications="notifications"
          :filters="filters"
        />
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
  import { Head } from '@inertiajs/vue3';
  import AppLayout from '../../Layouts/AppLayout.vue';
  import MyNotificationsCardHeader from '../../Components/User/MyNotifications/MyNotificationsCardHeader.vue';
  import MyNotificationsDisplay from '../../Components/User/MyNotifications/MyNotificationsDisplay.vue';
  import { ref } from 'vue';
  import { router } from '@inertiajs/vue3';

  const props = defineProps({
    previousUrl:String,
    user:Object,
    notifications:Object,
    filters: {
      type: Object,
      default: () => ({})
    },
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

    if (newFilters.read_at) {
      params.read_at = newFilters.read_at;
    }

    if (newFilters.sort) {
      params.sort = newFilters.sort;
    }
  
    router.get('/users/my-notifications', params, {
      preserveState: true,
      preserveScroll: false,
      replace: true,
    });
  };
</script>