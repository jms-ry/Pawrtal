<template>
  <Head title="Dashboard"></Head>
  <AppLayout>
    <div class="card mt-2 mt-md-3 mb-4 mb-md-2 border-0 me-2 me-md-5 ms-2 ms-md-5 px-1 px-md-5">
      <div class="card-body border-0 p-2 p-md-5">
        <div class="container-fluid py-4">
          <div class="card-header border-0 bg-secondary">
            <h3 class="text-center fw-bolder display-6 font-monospace mb-4">Organizational Dashboard</h3>
          </div>
          <RescuesSection
            :rescues="rescues"
          />
          <ReportsSection
            :reports="reports"
          />
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
  import { Head } from '@inertiajs/vue3';
  import AppLayout from '../../Layouts/AppLayout.vue';
  import RescuesSection from '../../Components/Dashboard/RescuesSection.vue';
  import ReportsSection from '../../Components/Dashboard/ReportsSection.vue';

  const props = defineProps({
    rescues: {
      type: Array,
      required: true
    },
    reports: {
      type:Array,
      required: true
    }
  })
</script>

<style scoped>
  .card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
  }

  .card-body h2 {
    font-size: 2.5rem;
    font-weight: bold;
    margin: 0;
  }
</style>