<template>
  <div class="modal fade me-2" id="createRescueProfileModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="createRescueProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-sm-scrollable modal-lg">
      <div class="modal-content ">
        <div class="modal-header bg-info-subtle">
          <i class="bi bi-plus-circle-fill me-3 text-primary fs-2"></i>
          <h5 class="modal-title">Create a New Rescue Profile</h5>
        </div>
        <form @submit="submitForm" class="" enctype="multipart/form-data">
        <div class="modal-body bg-info-subtle border-0">
          <div class="row g-2 mt-2">
            <div class="col-12 col-md-4 form-floating">
              <input type="text" name="name" class="form-control" placeholder="Rescue name" aria-label="Rescue name" id="floating_rescue_name" autocomplete="true" required>
              <label for="floating_rescue_name" class="form-label fw-bold">Name</label>
            </div>
            <div class="col-12 col-md-4 form-floating">
              <input type="text" name="species" id="floating_rescue_species" class="form-control" placeholder="Rescue name" aria-label="Rescue name" autocomplete="true" required>
              <label for="floating_rescue_species" class="form-label fw-bold">Species</label>
            </div>
            <div class="col-12 col-md-4 form-floating">
              <input type="text" name="breed" class="form-control" placeholder="Rescue breed" aria-label="Rescue breed" id="floating_rescue_breed" >
              <label for="floating_rescue_breed" class="form-label fw-bold">Breed</label>
            </div>
          </div>

          <div class="row g-2 mt-2">
            <div class="col-12 col-md-4 form-floating">
              <select name="sex" id="floating_sex" class="form-select" aria-label="sex-select" required>
                <option selected hidden>Sex</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
              </select>
              <label for="floating_sex" class="form-label fw-bold">Select a sex</label>
            </div>
            <div class="col-12 col-md-4 form-floating">
              <input type="text" name="age" class="form-control" placeholder="Rescue age" aria-label="Rescue age" id="floating_rescue_age">
              <label for="floating_rescue_age" class="form-label fw-bold">Age (e.g 6 months old)</label>
            </div>
            <div class="col-12 col-md-4 form-floating">
              <select name="size" id="floating_rescue_size" class="form-select" aria-label="size-select" required>
                <option selected hidden>Size</option>
                <option value="small">Small</option>
                <option value="medium">Medium</option>
                <option value="large">Large</option>
              </select>
              <label for="floating_rescue_size" class="form-label fw-bold">Select size</label>
            </div>
          </div>

          <div class="row g-2 mt-2">
            <div class="col-12 col-md-4 form-floating">
              <input type="text" name="color" class="form-control" placeholder="Rescue color" aria-label="Rescue color" id="floating_rescue_color">
              <label for="floating_rescue_color" class="form-label fw-bold">Color</label>
            </div>
            <div class="col-12 col-md-4 form-floating">
              <input type="text" name="distinctive_features" class="form-control" placeholder="Rescue distinctive features" aria-label="Rescue distinctive features" id="floating_rescue_distinctive_features">
              <label for="floating_rescue_distinctive_features" class="form-label fw-bold">Distinctive Features</label>
            </div>
            <div class="col-12 col-md-4 form-floating">
              <select name="health_status" id="floating_health_status" class="form-select" aria-label="health-status-select" required>
                <option selected hidden>Health Status</option>
                <option value="healthy">Healthy</option>
                <option value="sick">Sick</option>
                <option value="injured">Injured</option>
              </select>
              <label for="floating_health_status" class="form-label fw-bold">Select health status</label>
            </div>
          </div>

          <div class="row g-2 mt-2">
            <div class="col-12 col-md-4 form-floating">
              <select name="vaccination_status" id="floating_vaccination_status" class="form-select" aria-label="vaccination-status-select" required>
                <option selected hidden>Vaccination Status</option>
                <option value="vaccinated">Vaccinated</option>
                <option value="partially_vaccinated">Partially Vaccinated</option>
                <option value="not_vaccinated">Not Vaccinated</option>
              </select>
              <label for="floating_vaccination_status" class="form-label fw-bold">Select vaccination status</label>
            </div>
            <div class="col-12 col-md-4 form-floating">
              <select name="spayed_neutered" id="floating_spayed_neutered" class="form-select" aria-label="spayed-neutered-select" required>
                <option selected hidden>Spay/Neutered</option>
                <option value="true">Yes</option>
                <option value="false">No</option>
              </select>
              <label for="floating_spayed_neutered" class="form-label fw-bold">Is it spayed/neutered?</label>
            </div>
            <div class="col-12 col-md-4 form-floating">
              <select name="adoption_status" id="floating_adoption_status" class="form-select" aria-label="adoption-status-select" required>
                <option selected hidden>Adoption Status</option>
                <option value="available">Available</option>
                <option value="unavailable">Unavailable</option>
                <option value="adopted">Adopted</option>
              </select>
              <label for="floating_adoption_status" class="form-label fw-bold">Select adoption status</label>
            </div>
          </div>

          <div class="row g-2 mt-2">
            <div class="col-12 col-md-6">
              <label for="profile_image" class="form-label fw-bold">Upload Profile Image</label>
              <input type="file" name="profile_image" id="profile_image" class="form-control" accept="image/*" required>
            </div>
            <div class="col-12 col-md-6">
              <label for="images" class="form-label fw-bold">Additional Image/s</label>
              <input type="file" name="images[]" id="images" class="form-control" accept="image/*" multiple>
            </div>
          </div>

          <div class="row g-2 mt-2">
            <div class="col-12 form-floating">
              <textarea name="description" id="floating_rescue_description" class="form-control" placeholder="Rescue description" aria-label="Rescue description" style="height: 100px" required></textarea>
              <label for="floating_rescue_description" class="form-label fw-bold">Description</label>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-info-subtle">
          <button class="btn btn-primary me-1" type="submit">Create Rescue Profile</button>
          <button class="btn btn-danger" type="button"  data-bs-dismiss="modal">Close</button>
        </div>
      </form>
      </div>
    </div>
  </div>
</template>
<script>
  import {Inertia} from '@inertiajs/inertia'

  export default{
    name:'CreateRescueProfileModal',
    methods: {
      submitForm (event){
        event.preventDefault()
        const formData = new FormData(event.target)
        Inertia.post('/rescues', formData, {
          forceFormData: true
        })
      }
    }
  }
</script>