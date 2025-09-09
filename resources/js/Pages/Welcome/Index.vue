<template>
  <Head title="Welcome" />

  <AppLayout>
    <div>
      <WelcomeCarousel />
    </div>

    <div>
      <AboutUs />
    </div>

    <div>
      <RescueStatistics
        :sheltered-count="shelteredCount"
        :spayed-neutered-count="spayedNeuteredCount"
        :vaccinated-count="vaccinatedCount"
        :adopted-count="adoptedCount"
      />
    </div>

    <div>
      <OSO />
    </div>

    <div>
      <HowYouCanHelp />
    </div>

    <div>
    <MeetFewRescues 
      :rescues ="rescues"/>
    </div>

  </AppLayout>
  
  
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import WelcomeCarousel from '@/Components/Welcome/WelcomeCarousel.vue'
import AboutUs from '@/Components/Welcome/AboutUs.vue'
import OSO from '@/Components/Welcome/OSO.vue'
import HowYouCanHelp from '../../Components/Welcome/HowYouCanHelp.vue'
import RescueStatistics from "@/Components/Welcome/RescueStatistics.vue";
import MeetFewRescues from '../../Components/Welcome/MeetFewRescues.vue'
import AppLayout from '../../Layouts/AppLayout.vue'

const props = defineProps({
  shelteredCount: Number,
  spayedNeuteredCount: Number,
  vaccinatedCount: Number,
  adoptedCount: Number,
  rescues: Array,
});

</script>
