<template>
  <Head title="My Schedules"></Head>
  <AppLayout>
    <div class="container-fluid">
      <div class="card border-0 p-md-5">
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
  import { Head } from '@inertiajs/vue3';
  import AppLayout from '../../Layouts/AppLayout.vue';

  const props = defineProps({
    previousUrl:String,
    user:Object
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