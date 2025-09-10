<template>
  <div class="card-header align-items-start border-0 px-2 px-md-5 mx-0 mx-md-5 border-0 mb-0 bg-secondary mt-2">
    <h5 class="fw-bolder fs-3 font-monospace mb-2 mt-4 ms-md-5 ms-3"><span class="ms-md-5"></span><span class="ms-md-5"></span><strong class="text-start ms-md-5">Household Information</strong> </h5>
  </div>
  <div class="card-body border-0 px-md-5 mx-md-5" data-controller="household">
    <div class="container px-md-5">
      <hr class="mt-0 mb-4">
      <div class="bg-warning-subtle p-md-3 mt-3 mt-md-0 rounded-3">
        <div v-if="user?.household" class="container-fluid justify-content-center">
          <UpdateHouseholdForm 
            :user="user"
          />
        </div>
        <div v-else class="container-fluid justify-content-center">
          <CreateHouseholdForm 
            :user="user"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  import CreateHouseholdForm from './Forms/CreateHouseholdForm.vue';
  import UpdateHouseholdForm from './Forms/UpdateHouseholdForm.vue';

  const props = defineProps({
    user: {
      type: Object,
      required: true
    },
  })
</script>