<template>
  <div v-if="success || error || warning || info">
    <!-- Success -->
    <div v-if="success" class="alert alert-primary alert-dismissible fade show mt-2" role="alert" >
      <strong> <i class="bi bi-check-circle-fill me-1"></i> {{ success }}</strong>
    </div>

    <!-- Error -->
    <div v-if="error" class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
      <strong> <i class="bi bi-x-circle-fill me-1"></i> {{ error }}</strong>
    </div>

    <div v-if="warning" class="alert alert-warning alert-dismissible fade show mt-2" role="alert">
      <strong> <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ warning }}</strong>
    </div>

    <div v-if="info" class="alert alert-info alert-dismissible fade show mt-2" role="alert">
      <strong> <i class="bi bi-info-circle-fill me-1"></i> {{ info }}</strong>
    </div>
  </div>
</template>

<script setup>
  import { usePage } from '@inertiajs/vue3'
  import { ref, watch } from 'vue'

  const page = usePage()
  const success = ref(page.props.flash.success || null)
  const error = ref(page.props.flash.error || null)
  const warning = ref(page.props.flash.warning || null)
  const info = ref(page.props.flash.info || null)

  watch(
    () => page.props.flash,
    (newFlash) => {
      success.value = newFlash.success
      error.value = newFlash.error
      warning.value = newFlash.warning
      info.value = newFlash.warning
    }
  )
</script>
