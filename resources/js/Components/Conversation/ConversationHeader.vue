<template>
  <div class="card-header border-0 bg-secondary">
    <h4 class="fs-5 mb-md-4 mb-4 mt-3 mt-md-0 me-5 align-items-start">
      <a :href="previousUrl" class="text-decoration-none font-monospace fw-bolder text-danger fs-4"><i class="bi bi-chevron-left"></i><span class="ms-0">Back </span></a>
    </h4>
  </div>
</template>

<script setup>
  const props = defineProps({
    previousUrl:String,
  })
</script>