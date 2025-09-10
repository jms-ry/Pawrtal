<template>
  <Head title="Reports"></Head>
  <AppLayout>
    <div class="card mt-0 mt-md-4 mb-4 mb-md-2 border-0 me-2 me-md-5 ms-2 ms-md-5 px-1 px-md-5">
      <div class="card-body border-0 p-2 p-md-5">
        <ReportsCardHeader 
          :lostModal = "lostModal"
          :foundModal = "foundModal"
          :user="user"
        />
        <ReportsDisplay
          :reports = "reports"
          :user="user"
        />
      </div>
    </div>
  </AppLayout>
  
</template>

<script setup>
  import { Head } from '@inertiajs/vue3';
  import ReportsCardHeader from '../../Components/Reports/ReportsCardHeader.vue';
  import ReportsDisplay from '../../Components/Reports/ReportsDisplay.vue';
import AppLayout from '../../Layouts/AppLayout.vue';

  const props = defineProps({
  lostModal: String,
  foundModal: String,
  user: Object,
  reports: Array,
})
</script>