<template>
  <form @submit="submitForm" method="POST" class="">
    <input type="hidden" name="_token" :value="csrfToken" />
    <div class="card bg-warning-subtle border-0 p-3 p-md-5">
      <input type="hidden" name="user_id" :value="user.id">
      <div class="row g-2">
        <div class="col-12 col-md-6 form-floating">
          <input type="text" name="barangay" class="form-control" placeholder="Barangay" aria-label="Barangay" id="floating_barangay" :value="user.address.barangay">
          <label for="floating_barangay" class="form-label">Barangay</label>
        </div>
        <div class="col-12 col-md-6 form-floating">
          <input type="text" name="municipality" class="form-control" placeholder="Municipality" aria-label="Municipality" id="floating_municipality" :value="user.address.municipality">
          <label for="floating_municipality" class="form-label">Municipality</label>
        </div>
      </div>
      <div class="row g-2 mt-2">
        <div class="col-12 col-md-6 form-floating">
          <input type="text" id="floating_province" name="province" class="form-control" placeholder="Province" aria-label="Province" :value="user.address.province">
          <label for="floating_province" class="form-label">Province</label>
        </div>
        <div class="col-12 col-md-6 form-floating">
          <input type="text" id="floating_zip_code" name="zip_code" class="form-control" placeholder="Zip Code" aria-label="Zip Code" :value="user.address.zip_code">
          <label for="floating_zip_code" class="form-label">Zip Code</label>
        </div>
      </div>
    </div>
    <div class="card-footer border-0 bg-warning-subtle">
      <div class="justify-content-end d-none d-md-flex mt-3 mt-md-0">
        <button type="submit" class="btn btn-success fw-bolder me-2">Update Address</button>
        <button type="button" data-bs-toggle="modal" data-bs-target="#deleteAddressModal" class="btn btn-danger fw-bolder"
          :data-address-id="user.address.id">Delete Address
        </button>
      </div>
      <div class="d-md-none">
        <button type="submit" class="btn btn-success w-100 fw-bolder">Update Address</button>
        <button type="button" data-bs-toggle="modal" data-bs-target="#deleteAddressModal" class="btn btn-danger w-100 fw-bolder mt-2"
          :data-address-id="user.address.id">Delete Address
        </button>
      </div>
    </div>
  </form>
</template>

<script setup>
  import { Inertia } from '@inertiajs/inertia'

  const props = defineProps({
    user: {
      type: Object,
      default: () => null
    },
    csrfToken: {
      type: String,
      required: true
    },
  })

  function submitForm(event) {
    event.preventDefault()
    const formData = new FormData(event.target)
    Inertia.put(route('addresses.update', user.address.id), formData)
  }
</script>