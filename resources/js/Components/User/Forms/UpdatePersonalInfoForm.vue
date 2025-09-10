<template>
  <form @submit.prevent="submitForm" class="">
    <div class="card bg-warning-subtle border-0 p-3 p-md-5">
      <div class="row g-2">
        <div class="col-12 col-md-6 form-floating">
          <input type="text" name="first_name" class="form-control" placeholder="First name" aria-label="First name" id="floating_first_name" v-model="form.first_name">
          <label for="floating_first_name" class="form-label">First Name</label>
        </div>
        <div class="col-12 col-md-6 form-floating">
          <input type="text" name="last_name" class="form-control" placeholder="Last name" aria-label="Last name" id="floating_last_name" v-model="form.last_name">
          <label for="floating_last_name" class="form-label">Last Name</label>
        </div>
      </div>
      <div class="row g-2 mt-2">
        <div class="col-12 col-md-6 form-floating">
          <input type="tel" id="floating_contact_number" name="contact_number" class="form-control" placeholder="Contact Number" aria-label="Contact Number" v-model="form.contact_number">
          <label for="floating_contact_number" class="form-label">Contact Number</label>
        </div>
        <div class="col-12 col-md-6 form-floating">
          <input type="email" id="floating_email" name="email" class="form-control" placeholder="Email" aria-label="Email" v-model="form.email">
          <label for="floating_email" class="form-label">Email</label>
        </div>
      </div>
    </div>
    <div class="card-footer border-0 bg-warning-subtle">
      <div class="justify-content-end d-none d-md-flex mt-3 mt-md-0">
        <button type="submit" class="btn btn-info fw-bolder">Update Information</button>
      </div>
      <div class="d-md-none">
        <button type="submit" class="btn btn-info w-100 fw-bolder">Update Information</button>
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
    }
  })

  const form = useForm({
    first_name: props.user.first_name,
    last_name: props.user.last_name,
    contact_number: props.user.contact_number,
    email: props.user.email
  })

  function submitForm(){
    form.put(`/users/${props.user.id}`,{
      preserveScroll: true,
      preserveState: false,
    })
  }
  
</script>