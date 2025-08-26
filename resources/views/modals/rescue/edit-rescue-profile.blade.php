<div class="modal fade me-2" id="editRescueProfileModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editRescueProfileModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-sm-scrollable modal-lg">
    <div class="modal-content ">
      <div class="modal-header bg-info-subtle">
        <i class="bi bi-plus-circle-fill me-3 text-primary fs-2"></i>
        <h5 class="modal-title">Update {{ $rescue->name_formatted }}'s Profile</h5>
      </div>
      <form action="{{ route('rescues.update', $rescue->id) }}" method="POST" class="" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="modal-body bg-info-subtle border-0">
          <div class="row g-2 mt-2">
            <div class="col-12 col-md-4 form-floating">
              <input type="text" name="name" class="form-control" placeholder="Rescue name" aria-label="Rescue name" id="floating_rescue_name" autocomplete="true" value="{{ $rescue->name }}" required>
              <label for="floating_rescue_name" class="form-label fw-bold">Name</label>
            </div>
            <div class="col-12 col-md-4 form-floating">
              <input type="text" name="species" id="floating_rescue_species" class="form-control" placeholder="Rescue name" aria-label="Rescue name" autocomplete="true" value="{{ $rescue->species }}" required>
              <label for="floating_rescue_species" class="form-label fw-bold">Species</label>
            </div>
            <div class="col-12 col-md-4 form-floating">
              <input type="text" name="breed" class="form-control" placeholder="Rescue breed" aria-label="Rescue breed" id="floating_rescue_breed" value="{{ $rescue->breed }}">
              <label for="floating_rescue_breed" class="form-label fw-bold">Breed</label>
            </div>
          </div>

          <div class="row g-2 mt-2">
            <div class="col-12 col-md-4 form-floating">
              <select name="sex" id="floating_sex" class="form-select" aria-label="sex-select" required>
                <option hidden @selected($rescue->sex ==='') >Sex</option>
                <option value="male" @selected($rescue->sex === 'male') >Male</option>
                <option value="female" @selected($rescue->sex === 'female') >Female</option>
              </select>
              <label for="floating_sex" class="form-label fw-bold">Select a sex</label>
            </div>
            <div class="col-12 col-md-4 form-floating">
              <input type="text" name="age" class="form-control" placeholder="Rescue age" aria-label="Rescue age" id="floating_rescue_age" value="{{ $rescue->age }}">
              <label for="floating_rescue_age" class="form-label fw-bold">Age (e.g 6 months old)</label>
            </div>
            <div class="col-12 col-md-4 form-floating">
              <select name="size" id="floating_rescue_size" class="form-select" aria-label="size-select" required>
                <option hidden >Size</option>
                <option value="small" @selected($rescue->size === 'small') >Small</option>
                <option value="medium" @selected($rescue->size === 'medium') >Medium</option>
                <option value="large" @selected($rescue->size === 'large') >Large</option>
              </select>
              <label for="floating_rescue_size" class="form-label fw-bold">Select size</label>
            </div>
          </div>

          <div class="row g-2 mt-2">
            <div class="col-12 col-md-4 form-floating">
              <input type="text" name="color" class="form-control" placeholder="Rescue color" aria-label="Rescue color" id="floating_rescue_color" value="{{ $rescue->color }}">
              <label for="floating_rescue_color" class="form-label fw-bold">Color</label>
            </div>
            <div class="col-12 col-md-4 form-floating">
              <input type="text" name="distinctive_features" class="form-control" placeholder="Rescue distinctive features" aria-label="Rescue distinctive features" id="floating_rescue_distinctive_features" value="{{ $rescue->distinctive_features }}">
              <label for="floating_rescue_distinctive_features" class="form-label fw-bold">Distinctive Features</label>
            </div>
            <div class="col-12 col-md-4 form-floating">
              <select name="health_status" id="floating_health_status" class="form-select" aria-label="health-status-select" required>
                <option hidden>Health Status</option>
                <option value="healthy" @selected($rescue->health_status === 'healthy')>Healthy</option>
                <option value="sick" @selected($rescue->health_status === 'sick')>Sick </option>
                <option value="injured" @selected($rescue->health_status === 'injured')>Injured</option>
              </select>
              <label for="floating_health_status" class="form-label fw-bold">Select health status</label>
            </div>
          </div>

          <div class="row g-2 mt-2">
            <div class="col-12 col-md-4 form-floating">
              <select name="vaccination_status" id="floating_vaccination_status" class="form-select" aria-label="vaccination-status-select" required>
                <option hidden>Vaccination Status</option>
                <option value="vaccinated" @selected($rescue->vaccination_status === 'vaccinated')>Vaccinated</option>
                <option value="partially_vaccinated" @selected($rescue->vaccination_status === 'partially_vaccinated')>Partially Vaccinated</option>
                <option value="not_vaccinated" @selected($rescue->vaccination_status === 'not_vaccinated')>Not Vaccinated</option>
              </select>
              <label for="floating_vaccination_status" class="form-label fw-bold">Select vaccination status</label>
            </div>
            <div class="col-12 col-md-4 form-floating">
              <select name="spayed_neutered" id="floating_spayed_neutered" class="form-select" aria-label="spayed-neutered-select" required>
                <option hidden>Spay/Neutered</option>
                <option value="true" @selected($rescue->spayed_neutered)>Yes</option>
                <option value="false" @selected(!($rescue->spayed_neutered))>No</option>
              </select>
              <label for="floating_spayed_neutered" class="form-label fw-bold">Is it spayed/neutered?</label>
            </div>
            <div class="col-12 col-md-4 form-floating">
              <select name="adoption_status" id="floating_adoption_status" class="form-select" aria-label="adoption-status-select" required>
                <option hidden>Adoption Status</option>
                <option value="available" @selected($rescue->adoption_status === 'available')>Available</option>
                <option value="unavailable" @selected($rescue->adoption_status === 'unavailable')>Unavailable</option>
                <option value="adopted" @selected($rescue->adoption_status === 'adopted')>Adopted</option>
              </select>
              <label for="floating_adoption_status" class="form-label fw-bold">Select adoption status</label>
            </div>
          </div>

          <div class="row g-2 mt-2">
            <div class="col-12 col-md-6">
              <label for="profile_image" class="form-label fw-bold">Change Profile Image</label>
              <input type="file" name="profile_image" id="profile_image" class="form-control" accept="image/*">
              <small class="text-muted mt-3">Leave blank to keep existing image</small>
              @if($rescue->profile_image)
                <div class="mb-2 mt-2">
                  <img src="{{ $rescue->profile_image_url }}" alt="{{ $rescue->name }}" class="w-100 h-100 object-fit-cover rounded-4" style="max-height: 150px;">
                </div>
              @endif
            </div>
            <div class="col-12 col-md-6">
              <label for="images" class="form-label fw-bold">Upload Additional Image/s</label>
              <input type="file" name="images[]" id="images" class="form-control" accept="image/*" value="images" multiple>
            </div>
          </div>

          <div class="row g-2 mt-2">
            <div class="col-12 form-floating">
              <textarea name="description" id="floating_rescue_description" class="form-control" placeholder="Rescue description" aria-label="Rescue description" style="height: 100px" required>{{ $rescue->description }}</textarea>
              <label for="floating_rescue_description" class="form-label fw-bold">Description</label>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-info-subtle">
          <button class="btn btn-primary me-1" type="submit">Update Rescue Profile</button>
          <button class="btn btn-danger" type="button"  data-bs-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>