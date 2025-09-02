<template>
  <!--Rescue Statistics Section-->
  <div class="card mt-4 border-0 bg-warning mb-5" data-controller="rescues-statistics">
    <div class="card-body border-0 p-3 p-md-5">
      <div class="container-fluid">
        <div class="row g-0 d-flex align-items-center justify-content-center text-center p-3 p-md-5">
          <div class="col-12 col-md-3 mb-4 mb-md-0">
            <span 
              class="display-5 fw-bolder" 
              id="sheltered-count" 
              :data-count="shelteredCount"
            >0</span>
            <h6 class="fw-bold fs-4 mt-2 font-monospace">Sheltered</h6>
          </div>

          <div class="col-12 col-md-3 mb-4 mb-md-0">
            <span 
              class="display-5 fw-bolder" 
              id="spayed-neutered-count" 
              :data-count="spayedNeuteredCount"
            >0</span>
            <h6 class="fw-bold fs-4 mt-2 font-monospace">Spay/Neutered</h6>
          </div>

          <div class="col-12 col-md-3 mb-4 mb-md-0">
            <span 
              class="display-5 fw-bolder" 
              id="vaccinated-count" 
              :data-count="vaccinatedCount"
            >0</span>
            <h6 class="fw-bold fs-4 mt-2 font-monospace">Fully Vaccinated</h6>
          </div>

          <div class="col-12 col-md-3 mb-4 mb-md-0">
            <span 
              class="display-5 fw-bolder" 
              id="adopted-count" 
              :data-count="adoptedCount"
            >0</span>
            <h6 class="fw-bold fs-4 mt-2 font-monospace">Adopted/Rehomed</h6>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--End Rescue Statistics Section-->
</template>

<script setup>
defineProps({
  shelteredCount: {
    type: Number,
    required: true
  },
  spayedNeuteredCount: {
    type: Number,
    required: true
  },
  vaccinatedCount: {
    type: Number,
    required: true
  },
  adoptedCount: {
    type: Number,
    required: true
  }
})
</script>
