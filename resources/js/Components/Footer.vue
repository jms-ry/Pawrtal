<template>
  <footer class="bg-dark text-white text-center d-flex justify-content-center align-items-center fw-bold">
    <p class="p-2 mt-3 mb-3">&copy; {{ currentYear }} Pawrtal. All rights reserved.</p>
  </footer>
</template>

<script setup>
  import { ref } from 'vue'

  // Get current year dynamically (similar to Blade's `date('Y')`)
  const currentYear = ref(new Date().getFullYear())
</script>
