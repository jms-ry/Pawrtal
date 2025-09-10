<template>
  <form @submit="submitForm" method="POST">
    <div class="card bg-warning-subtle border-0 p-3 p-md-5">
      <input type="hidden" name="user_id" class="form-control" :value="user?.id">
      <div class="row g-2">
        <div class="col-12 col-md-6 form-floating">
          <input type="text" name="barangay" class="form-control" placeholder="Barangay" aria-label="Barangay" id="floating_barangay">
          <label for="floating_barangay" class="form-label">Barangay</label>
        </div>
        <div class="col-12 col-md-6 form-floating">
          <input type="text" name="municipality" class="form-control" placeholder="Municipality" aria-label="Municipality" id="floating_municipality">
          <label for="floating_municipality" class="form-label">Municipality</label>
        </div>
      </div>
      <div class="row g-2 mt-2">
        <div class="col-12 col-md-6 form-floating">
          <input type="text" id="floating_province" name="province" class="form-control" placeholder="Province" aria-label="Province">
          <label for="floating_province" class="form-label">Province</label>
        </div>
        <div class="col-12 col-md-6 form-floating">
          <input type="text" id="floating_zip_code" name="zip_code" class="form-control" placeholder="Zip Code" aria-label="Zip Code">
          <label for="floating_zip_code" class="form-label">Zip Code</label>
        </div>
      </div>
    </div>
    <div class="card-footer border-0 bg-warning-subtle">
      <div class="justify-content-end d-none d-md-flex mt-3 mt-md-0">
        <button type="submit" class="btn btn-success fw-bolder">Create Address</button>
      </div>
      <div class="d-md-none">
        <button type="submit" class="btn btn-success w-100 fw-bolder">Create Address</button>
      </div>
    </div>
  </form>
</template>

<script setup>
  import { router} from '@inertiajs/vue3'

  const props = defineProps({
    user: {
      type: Object,
      default: () => null
    },
  })

  function submitForm(event) {
    event.preventDefault()
    const formData = new FormData(event.target)
    
    router.post('/addresses',formData,{
      preserveScroll: false,
      preserveState: false,
    })
  }
</script>