<template>
  <div class="container px-md-5 mb-5">
    <hr class="mt-3">
    <div class="bg-warning-subtle p-md-3 mt-3 mt-md-0 rounded-3">
      <h3 class="text-center fw-bold font-monospace mt-3 mt-md-0 p-2 p-md-0">Gallery</h3>
      <div v-if="notEmpty" class="row g-3 mt-3 justify-content-center">
        <div v-for="image in randomImages" class="col-12 col-md-4 shadow-sm rounded-4 bg-warning-subtle">
          <div class="ratio ratio-4x3 p-0 p-md-2 mt-1 rescue-gallery">
            <img :src="image" alt="Gallery image" class="w-100 h-100 object-fit-cover rounded-4">
          </div>
        </div>
      </div>
      <div v-else class="mt-1 mt-md-5">
        <div class="d-flex justify-content-center text-center mx-auto">
          <p class="badge bg-warning-subtle fs-5 fw-lighter text-muted fst-italic font-monospace border-0 mb-5 mt-3 text-center mx-auto"> <i class="bi bi-image text-dark-subtle"></i> No Images Available...</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>

  const props = defineProps({
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