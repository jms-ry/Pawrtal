<template>
  <Head title="Rescue/Show"></Head>
  <AppLayout>
    <div class="container-fluid">
      <div class="card border-0 p-md-5">
        <RescueCardHeader 
          :rescue="rescue"
          :previousUrl="previousUrl"
          :urlText="urlText"
        />

        <div class="card-body border-0 px-md-5 mx-md-5">
          <RescueDisplay 
            :rescue="rescue"
            :user="user"
          />

          <RescueGallery 
            :notEmpty="notEmpty"
            :randomImages="randomImages"
          />
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
  import { Head } from '@inertiajs/vue3';
  import RescueCardHeader from '../../Components/Rescues/Show/RescueCardHeader.vue';
  import RescueDisplay from '../../Components/Rescues/Show/RescueDisplay.vue';
  import RescueGallery from '../../Components/Rescues/Show/RescueGallery.vue';
  import AppLayout from '../../Layouts/AppLayout.vue';

  const props = defineProps({
    user: {
      type: Object,
      default: null,
    },

    rescue:{
      type: Object,
      required: true
    },
    previousUrl: String,
    urlText: String,
    notEmpty: {
      type: Boolean,
      default: null,
    },

    randomImages: {
      type: Array,
      default: () => []
    }
  })
</script>