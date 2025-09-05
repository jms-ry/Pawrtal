<template>
  <form @submit="submitForm" method="POST">
    <div class="card bg-warning-subtle border-0 p-3 p-md-5">
      <input type="hidden" name="user_id" class="form-control" :value="user.id">
      <div class="row g-2">
        <div class="col-12 col-md-6 form-floating">
          <input type="text" name="house_structure" class="form-control" placeholder="House Structure" aria-label="House Structure" id="floating_house_structure" >
          <label for="floating_house_structure" class="form-label">House Structure (e.g Apartment, Tiny House, Cabin, etc.)</label>
        </div>
        <div class="col-12 col-md-6 form-floating">
          <input type="number" min="1" name="household_members" class="form-control" placeholder="House Members" aria-label="House Members" id="floating_house_members">
          <label for="floating_house_members" class="form-label">House members (including yourself)</label>
        </div>
      </div>
      <div class="row g-2 mt-2">
        <div class="col-12 col-md-6 form-floating">
          <select name="have_children" id="floating_have_children" class="form-select" aria-label="children-select" data-action="change->household#toggleNumberOfChildrenField" required>
            <option hidden >Are there children in the house?</option>
            <option value="true" >Yes</option>
            <option value="false" >No</option>
          </select>
          <label for="floating_have_children" class="form-label">Are there children in the house?</label>
        </div>
        <div class="col-12 col-md-6 form-floating d-none" id="number_of_children_div">
          <input type="number" min="1" name="number_of_children" class="form-control" placeholder="Number of Children" aria-label="Number of Children" id="floating_number_of_children">
          <label for="floating_number_of_children" class="form-label">Number of Children</label>
        </div>
      </div>
      <div class="row g-2 mt-2">
        <div class="col-12 col-md-4 form-floating">
          <select name="has_other_pets" id="floating_has_other_pets" class="form-select" aria-label="other-pet-select" data-action="change->household#togglePetsFields" required>
            <option hidden >Do you have other pets?</option>
            <option value="true" >Yes</option>
            <option value="false" >No</option>
          </select>
          <label for="floating_has_other_pets" class="form-label">Do you have other pets?</label>
        </div>
        <div class="col-12 col-md-4 form-floating d-none" id="current_pets_div">
          <input type="text" id="floating_current_pets" name="current_pets" class="form-control" placeholder="Current Pet/s" aria-label="Current Pet/s">
          <label for="floating_current_pets" class="form-label">Current Pet/s (Separate with , if more than one) </label>
        </div>
        <div class="col-12 col-md-4 form-floating d-none" id="number_of_current_pets_div">
          <input type="number" min="1" id="floating_number_of_current_pets" name="number_of_current_pets" class="form-control" placeholder="Number of Current Pet/s" aria-label="Number of Current Pet/s">
          <label for="floating_number_of_current_pets" class="form-label">Number of Current Pet/s</label>
        </div>
      </div>
    </div>
    <div class="card-footer border-0 bg-warning-subtle">
      <div class="justify-content-end d-none d-md-flex mt-3 mt-md-0">
        <button type="submit" class="btn btn-success fw-bolder">Create Household</button>
      </div>
      <div class="d-md-none">
        <button type="submit" class="btn btn-success w-100 fw-bolder">Create Household</button>
      </div>
    </div>
  </form>
</template>

<script setup>
  import {Inertia} from '@inertiajs/inertia'

  const props = defineProps({
    user: {
      type: Object,
      default: () => null
    },
  })

  function submitForm(event) {
    event.preventDefault()
    const formData = new FormData(event.target)
    Inertia.post('/households', formData)
  }
</script>