<template>
  <Head title="Rescues"></Head>
  <AppLayout>
    <div class="card mt-2 mt-md-3 mb-4 mb-md-2 border-0 me-2 me-md-5 ms-2 ms-md-5 px-1 px-md-5">
      <div class="card-body border-0 p-2 p-md-5">
        <RescueCardHeader
          :user = "user"
        />
        <RescuesDisplay
          :user = "user" 
          :rescues = "rescues"
        />
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
  import { Head } from '@inertiajs/vue3';
  import RescueCardHeader from '@/Components/Rescues/RescueCardHeader.vue';
  import RescuesDisplay from '@/Components/Rescues/RescuesDisplay.vue';
  import AppLayout from '../../Layouts/AppLayout.vue';
  const props = defineProps({
    user: {
      type: Object,
    },
    rescues: Array,
  });
  
</script>

