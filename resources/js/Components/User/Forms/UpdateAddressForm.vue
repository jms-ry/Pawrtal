<template>
  <form @submit.prevent="submitForm" method="POST" class="">
    <div class="card bg-warning-subtle border-0 p-3 p-md-5">
      <input type="hidden" name="user_id" v-model="form.user_id">
      <div class="row g-2">
        <div class="col-12 col-md-6 form-floating">
          <input type="text" name="barangay" class="form-control" placeholder="Barangay" aria-label="Barangay" id="floating_barangay" v-model="form.barangay">
          <label for="floating_barangay" class="form-label">Barangay</label>
        </div>
        <div class="col-12 col-md-6 form-floating">
          <input type="text" name="municipality" class="form-control" placeholder="Municipality" aria-label="Municipality" id="floating_municipality" v-model="form.municipality">
          <label for="floating_municipality" class="form-label">Municipality</label>
        </div>
      </div>
      <div class="row g-2 mt-2">
        <div class="col-12 col-md-6 form-floating">
          <input type="text" id="floating_province" name="province" class="form-control" placeholder="Province" aria-label="Province" v-model="form.province">
          <label for="floating_province" class="form-label">Province</label>
        </div>
        <div class="col-12 col-md-6 form-floating">
          <input type="text" id="floating_zip_code" name="zip_code" class="form-control" placeholder="Zip Code" aria-label="Zip Code" v-model="form.zip_code">
          <label for="floating_zip_code" class="form-label">Zip Code</label>
        </div>
      </div>
    </div>
    <div class="card-footer border-0 bg-warning-subtle">
      <div class="justify-content-end d-none d-md-flex mt-3 mt-md-0">
        <button type="submit" class="btn btn-info fw-bolder me-2">Update Address</button>
        <button type="button" data-bs-toggle="modal" data-bs-target="#deleteAddressModal" class="btn btn-danger fw-bolder"
          :data-address-id="user.address.id">Delete Address
        </button>
      </div>
      <div class="d-md-none">
        <button type="submit" class="btn btn-info w-100 fw-bolder">Update Address</button>
        <button type="button" data-bs-toggle="modal" data-bs-target="#deleteAddressModal" class="btn btn-danger w-100 fw-bolder mt-2"
          :data-address-id="user.address.id">Delete Address
        </button>
      </div>
    </div>
  </form>
</template>

<script setup>
  import { useForm } from '@inertiajs/vue3';

  const props = defineProps({
    user: {
      type: Object,
      default: () => null
    },
  })
    const form = useForm({
    user_id: props.user.id,
    barangay: props.user.address.barangay,
    municipality: props.user.address.municipality,
    province: props.user.address.province,
    zip_code: props.user.address.zip_code
  })
  function submitForm() {
    form.put(`/addresses/${props.user.address.id}`,{
      preserveScroll: false,
      preserveState: false,
    })
  }
</script>