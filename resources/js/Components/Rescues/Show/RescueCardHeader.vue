<template>
  <div class="card-header align-items-start border-0 px-2 px-md-5 mx-0 mx-md-5 border-0 mb-0 bg-secondary">
    <h4 class="fs-6 ms-md-5 mb-md-4 mb-2 mt-3 mt-md-0 me-5">
      <a :href="previousUrl" class="text-decoration-none font-monospace fw-bolder text-danger fs-4"><i class="bi bi-chevron-left"></i><span class="ms-0">Back {{ urlText }}</span></a>
    </h4>
    <h3 class="fw-bolder display-6 font-monospace mb-2 mt-3 ms-md-5 ms-3">{{ rescue.name }}</h3>
  </div>
</template>

<script setup>

  const props = defineProps ({
    rescue: {
      type: Object,
      required: true
    },
    previousUrl: String,
    urlText: String,
  })
</script>