<template>
  <Head title="Rescues"></Head>
  <AppLayout>
    <div class="card mt-2 mt-md-3 mb-4 mb-md-2 border-0 me-2 me-md-5 ms-2 ms-md-5 px-1 px-md-5">
      <div class="card-body border-0 p-2 p-md-5">
        <RescuesCardHeader
          :user = "user"
          :search="filters.search"
          @search="handleSearch"
        />
        <RescuesDisplay
          :user = "user" 
          :rescues = "rescues"
          :search="filters.search"
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
      const params = {};
    
      if (searchTerm && searchTerm.trim()) {
        params.search = searchTerm.trim();
      }
    
      router.get(`/rescues`, params, {
        preserveState: true,
        preserveScroll: false, 
        replace: true,
      });
    }, 500);
  };
</script>

