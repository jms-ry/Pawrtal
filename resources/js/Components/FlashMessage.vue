<template>
  <div v-if="success || error">
    <!-- Success -->
    <div v-if="success" class="alert alert-primary alert-dismissible fade show mt-2" role="alert" >
      <strong>{{ success }}</strong>
    </div>

    <!-- Error -->
    <div v-if="error" class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
      <strong>{{ error }}</strong>
    </div>
  </div>
</template>

<script setup>
  import { usePage } from '@inertiajs/vue3'
  import { ref, watch } from 'vue'

  const page = usePage()
  const success = ref(page.props.flash.success || null)
  const error = ref(page.props.flash.error || null)

  watch(
    () => page.props.flash,
    (newFlash) => {
      success.value = newFlash.success
      error.value = newFlash.error
    }
  )
</script>
