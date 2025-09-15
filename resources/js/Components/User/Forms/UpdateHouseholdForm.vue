<template>
  <form @submit.prevent="submitForm" class="">
    <div class="card bg-warning-subtle border-0 p-3 p-md-5">
      <input type="hidden" name="user_id" v-model="form.user_id">
      <div class="row g-2">
        <div class="col-12 col-md-6 form-floating">
          <input type="text" name="house_structure" class="form-control" placeholder="House Structure" aria-label="House Structure" id="floating_house_structure" v-model="form.house_structure">
          <label for="floating_house_structure" class="form-label">House Structure (e.g Apartment, Tiny House, Cabin, etc.)</label>
        </div>
        <div class="col-12 col-md-6 form-floating">
          <input type="number" min="1" name="household_members" class="form-control" placeholder="House Members" aria-label="House Members" id="floating_house_members" v-model="form.household_members">
          <label for="floating_house_members" class="form-label">House members (including yourself)</label>
        </div>
      </div>
      <div class="row g-2 mt-2">
        <div class="col-12 col-md-6 form-floating">
          <select v-model="form.have_children" name="have_children" id="floating_have_children" class="form-select" aria-label="children-select" data-action="change->household#toggleNumberOfChildrenField" required>
            <option hidden >Are there children in the house?</option>
            <option value="true">Yes</option>
            <option value="false">No</option>
          </select>
          <label for="floating_have_children" class="form-label">Are there children in the house?</label>
        </div>
        <div class="col-12 col-md-6 form-floating" v-show="form.have_children === 'true'">
          <input type="number" min="1" name="number_of_children" class="form-control" placeholder="Number of Children" aria-label="Number of Children" id="floating_number_of_children"  v-model="form.number_of_children">
          <label for="floating_number_of_children" class="form-label">Number of Children</label>
        </div>
      </div>
      <div class="row g-2 mt-2">
        <div class="col-12 col-md-4 form-floating">
          <select v-model="form.has_other_pets" name="has_other_pets" id="floating_has_other_pets" class="form-select" aria-label="other-pet-select">
            <option hidden >Do you have other pets?</option>
            <option value="true">Yes</option>
            <option value="false">No</option>
          </select>
          <label for="floating_has_other_pets" class="form-label">Do you have other pets?</label>
        </div>
        <div class="col-12 col-md-4 form-floating" v-show="form.has_other_pets === 'true'">
          <input type="text" id="floating_current_pets" name="current_pets" class="form-control" placeholder="Current Pet/s" aria-label="Current Pet/s"  v-model="form.current_pets">
          <label for="floating_current_pets" class="form-label">Current Pet/s (Separate with , if more than one) </label>
        </div>
        <div class="col-12 col-md-4 form-floating" v-show="form.has_other_pets === 'true'">
          <input type="number" min="1" id="floating_number_of_current_pets" name="number_of_current_pets" class="form-control" placeholder="Number of Current Pet/s" aria-label="Number of Current Pet/s"  v-model="form.number_of_current_pets">
          <label for="floating_number_of_current_pets" class="form-label">Number of Current Pet/s</label>
        </div>
      </div>
    </div>
    <div class="card-footer border-0 bg-warning-subtle">
      <div class="justify-content-end d-none d-md-flex mt-3 mt-md-0">
        <button type="submit" class="btn btn-info fw-bolder me-2">Update Household</button>
        <button type="button" data-bs-toggle="modal" data-bs-target="#deleteHouseholdModal" :data-household-id="user.household.id" class="btn btn-danger fw-bolder">Delete Household</button>
      </div>
      <div class="d-md-none">
        <button type="submit" class="btn btn-info w-100 fw-bolder">Update Household</button>
        <button type="button" data-bs-toggle="modal" data-bs-target="#deleteHouseholdModal" :data-household-id="user.household.id" class="btn btn-danger w-100 fw-bolder mt-2">Delete Household</button>
      </div>
    </div>
  </form>
</template>

<script setup>
  import { useForm } from '@inertiajs/vue3'

  const props = defineProps({
    user: {
      type: Object,
      required: true
    },
  })

  const form = useForm({
    user_id: props.user.id,
    house_structure: props.user.household.house_structure,
    household_members: props.user.household.household_members,
    have_children: props.user.household.have_children !== null && props.user.household.have_children !== undefined ?
     (props.user.household.have_children ? 'true' : 'false') : '',
    number_of_children: props.user.household.number_of_children,
    has_other_pets: props.user.household.has_other_pets!== null && props.user.household.has_other_pets !== undefined ?
     (props.user.household.has_other_pets ? 'true' : 'false') : '',
    current_pets:props.user.household.current_pets,
    number_of_current_pets: props.user.household.number_of_current_pets
  })
  function submitForm() {
    form.put(`/households/${props.user.household.id}`,{
      preserveScroll: false,
      preserveState: false,
      onError: (errors) => {
        console.error("Validation errors:", errors)
      }
    })
  }
</script>