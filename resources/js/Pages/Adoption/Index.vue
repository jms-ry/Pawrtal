<template>
  <Head title="Adoption"></Head>
  <AppLayout>
    <div class="card mt-2 mt-md-3 mb-4 mb-md-2 border-0 me-2 me-md-5 ms-2 ms-md-5 px-1 px-md-5">
      <div class="card-body border-0 p-2 p-md-5">
        <AdoptionCardHeader />

        <AdoptionDisplay 
          :user = "user"
          :adoptables = "adoptables"
        />
      </div>
    </div>
  </AppLayout>
  
</template>

<script setup>
  import { Head } from '@inertiajs/vue3';
  import AdoptionCardHeader from '../../Components/Adoption/AdoptionCardHeader.vue';
  import AdoptionDisplay from '../../Components/Adoption/AdoptionDisplay.vue';
  import AppLayout from '../../Layouts/AppLayout.vue';

  const props = defineProps({
    adoptables: {
      type: Array,
      default: () => []
    },
    user: {
      type: Object,
      default: () => null
    }
  });
</script>