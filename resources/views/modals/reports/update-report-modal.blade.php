<div class="modal fade" id="updateReportModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="updateReportModalLabel">
  <div class="modal-dialog modal-dialog-centered modal-dialog-sm-scrollable modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-info-subtle">
        <i class="bi bi-plus-circle-fill me-3 text-primary fs-2"></i>
        <h5 class="modal-title">Update <span id="modal-title-span"></span> Report!</h5>
      </div>
      <!--Lost Animal Report Form-->
      <form id="updateLostAnimalReportForm" class="lostAnimalReportForm d-none" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="modal-body bg-info-subtle border-0">
          <input type="hidden" name="type" class="form-control" value="lost">
          <input type="hidden" name="user_id" class="form-control" value="{{ $user?->id }}">
          <div class="row g-2 mt-2">
            <div class="col-12 col-md-4 form-floating">
              <input type="text" name="animal_name" class="form-control" placeholder="Animal name" aria-label="Animal name" id="floating_animal_name" autocomplete="true" required>
              <label for="floating_animal_name" class="form-label fw-bold">Animal's Name</label>
            </div>
            <div class="col-12 col-md-4 form-floating">
              <input type="text" name="species" class="form-control" placeholder="Animal species" aria-label="Animal species" id="floating_animal_species" autocomplete="true" required>
              <label for="floating_animal_species" class="form-label fw-bold">Species (e.g Dog, Cat, etc.)</label>
            </div>
            <div class="col-12 col-md-4 form-floating">
              <input type="text" name="breed" class="form-control" placeholder="Animal breed" aria-label="Animal breed" id="floating_animal_breed" autocomplete="true" required>
              <label for="floating_animal_breed" class="form-label fw-bold">Breed</label>
            </div>
          </div>

          <div class="row g-2 mt-2">
            <div class="col-12 col-md-4 form-floating">
              <input type="text" name="color" class="form-control" placeholder="Animal color" aria-label="Animal color" id="floating_animal_color" autocomplete="true" required>
              <label for="floating_animal_color" class="form-label fw-bold">Color</label>
            </div>
            <div class="col-12 col-md-4 form-floating">
              <select name="sex" id="floating_animal_sex" class="form-select" aria-label="sex-select" required>
                <option selected hidden>Sex</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="unknown">Unknown</option>
              </select>
              <label for="floating_animal_sex" class="form-label fw-bold">Select a sex</label>
            </div>
            <div class="col-12 col-md-4 form-floating">
              <input type="text" name="age_estimate" class="form-control" placeholder="Animal age estimate" aria-label="Animal age estimate" id="floating_animal_age_estimate" autocomplete="true" required>
              <label for="floating_animal_age_estimate" class="form-label fw-bold">Age Estimate (e.g 6 months old)</label>
            </div>
          </div>

          <div class="row g-2 mt-2">
            <div class="col-12 col-md-4 form-floating">
              <select name="size" id="floating_animal_size" class="form-select" aria-label="size-select" required>
                <option selected hidden>Size</option>
                <option value="small">Small</option>
                <option value="medium">Medium</option>
                <option value="large">Large</option>
              </select>
              <label for="floating_animal_size" class="form-label fw-bold">Select size</label>
            </div>
            <div class="col-12 col-md-8 form-floating">
              <input type="text" name="distinctive_features" class="form-control" placeholder="Animal distinctive features" aria-label="Animal distinctive features" id="floating_animal_distinctive_features">
              <label for="floating_animal_distinctive_features" class="form-label fw-bold">Distinctive Features</label>
            </div>
          </div>

          <div class="row g-2 mt-2">
            <div class="col-12 col-md-8 form-floating">
              <input type="text" name="last_seen_location" class="form-control" placeholder="Animal last seen location" aria-label="Animal last seen location" id="floating_animal_last_seen_location" required>
              <label for="floating_animal_last_seen_location" class="form-label fw-bold">Last Seen Location</label>
            </div>
            <div class="col-12 col-md-4 form-floating">
              <input type="date" name="last_seen_date" class="form-control" placeholder="Animal last seen date" aria-label="Animal last seen date" id="floating_animal_last_seen_date" required>
              <label for="floating_animal_last_seen_date" class="form-label fw-bold">Last Seen Date</label>
            </div>
          </div>

          <div class="row g-2 mt-2">
            <div class="col-8">
              <label for="image" class="form-label fw-bold">Update Image</label>
              <input type="file" name="image" id="image" class="form-control" accept="image/*">
              <small class="text-muted mt-3">Leave blank to keep existing image</small>
              <div class="mb-2 mt-2">
                <img id="reportImage" class="w-100 h-100 object-fit-cover rounded-4" style="max-height: 150px;">
              </div>
            </div>
            <div class="col-4">
              <label for="report_status" class="form-label fw-bold">Update Report Status</label>
              <select name="status" id="report_status" class="form-select" aria-label="size-select" required>
                <option selected hidden>Status</option>
                <option value="resolved">Resolved</option>
                <option value="active">Not yet resolved</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-info-subtle">
          <button class="btn btn-primary me-1" type="submit">Update Report</button>
          <button class="btn btn-danger" type="button"  data-bs-dismiss="modal">Close</button>
        </div>
      </form>
      <!--Found Animal Report Form-->
      <form id="updateFoundAnimalReportForm" class="foundAnimalReportForm d-none" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="modal-body bg-info-subtle border-0">
          <input type="hidden" name="type" class="form-control" value="found">
          <input type="hidden" name="user_id" class="form-control" value="{{ $user?->id }}">
          <div class="row g-2 mt-2">
            <div class="col-12 col-md-4 form-floating">
              <input type="text" name="species" class="form-control" placeholder="Animal species" aria-label="Animal species" id="floating_animal_species_found" autocomplete="true" required>
              <label for="floating_animal_species_found" class="form-label fw-bold">Species (e.g Dog, Cat, etc.)</label>
            </div>
            <div class="col-12 col-md-4 form-floating">
              <input type="text" name="breed" class="form-control" placeholder="Animal breed" aria-label="Animal breed" id="floating_animal_breed_found" autocomplete="true" required>
              <label for="floating_animal_breed_found" class="form-label fw-bold">Breed</label>
            </div>
            <div class="col-12 col-md-4 form-floating">
              <input type="text" name="color" class="form-control" placeholder="Animal color" aria-label="Animal color" id="floating_animal_color_found" autocomplete="true" required>
              <label for="floating_animal_color_found" class="form-label fw-bold">Color</label>
            </div>
          </div>

          <div class="row g-2 mt-2">
            <div class="col-12 col-md-4 form-floating">
              <select name="sex" id="floating_animal_sex_found" class="form-select" aria-label="sex-select" required>
                <option selected hidden>Sex</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="unknown">Unknown</option>
              </select>
              <label for="floating_animal_sex_found" class="form-label fw-bold">Select a sex</label>
            </div>
            <div class="col-12 col-md-4 form-floating">
              <input type="text" name="age_estimate" class="form-control" placeholder="Animal age estimate" aria-label="Animal age estimate" id="floating_animal_age_estimate_found" autocomplete="true" required>
              <label for="floating_animal_age_estimate_found" class="form-label fw-bold">Age Estimate (e.g 6 months old)</label>
            </div>
            <div class="col-12 col-md-4 form-floating">
              <select name="size" id="floating_animal_size_found" class="form-select" aria-label="size-select" required>
                <option selected hidden>Size</option>
                <option value="small">Small</option>
                <option value="medium">Medium</option>
                <option value="large">Large</option>
              </select>
              <label for="floating_animal_size_found" class="form-label fw-bold">Select size</label>
            </div>
          </div>

          <div class="row g-2 mt-2">
            <div class="col-12 col-md-4 form-floating">
              <input type="text" name="distinctive_features" class="form-control" placeholder="Animal distinctive features" aria-label="Animal distinctive features" id="floating_animal_distinctive_features_found">
              <label for="floating_animal_distinctive_features_found" class="form-label fw-bold">Distinctive Features</label>
            </div>
            <div class="col-12 col-md-4 form-floating">
              <input type="text" name="condition" class="form-control" placeholder="Animal condition" aria-label="Animal condition" id="floating_animal_condition_found">
              <label for="floating_animal_condition_found" class="form-label fw-bold">Condition</label>
            </div>
            <div class="col-12 col-md-4 form-floating">
              <input type="text" name="temporary_shelter" class="form-control" placeholder="Animal temporary" aria-label="Animal temporary shelter" id="floating_animal_temporary_shelter_found">
              <label for="floating_animal_temporary_shelter_found" class="form-label fw-bold">Temporary Shelter</label>
            </div>
          </div>

          <div class="row g-2 mt-2">
            <div class="col-12 col-md-8 form-floating">
              <input type="text" name="found_location" class="form-control" placeholder="Animal found location" aria-label="Animal found location" id="floating_animal_found_location" required>
              <label for="floating_animal_found_location" class="form-label fw-bold">Found Location</label>
            </div>
            <div class="col-12 col-md-4 form-floating">
              <input type="date" name="found_date" class="form-control" placeholder="Animal last seen date" aria-label="Animal found date" id="floating_animal_found_date" required>
              <label for="floating_animal_found_date" class="form-label fw-bold">Found Date</label>
            </div>
          </div>

          <div class="row g-2 mt-2">
            <div class="col-8">
              <label for="image_found" class="form-label fw-bold">Update Image</label>
              <input type="file" name="image" id="image_found" class="form-control" accept="image/*">
              <small class="text-muted mt-3">Leave blank to keep existing image</small>
              <div class="mb-2 mt-2">
                <img id="reportImageFound" class="w-100 h-100 object-fit-cover rounded-4" style="max-height: 150px;">
              </div>
            </div>
            <div class="col-4">
              <label for="report_status_found" class="form-label fw-bold">Update Report Status</label>
              <select name="status" id="report_status_found" class="form-select" aria-label="size-select" required>
                <option selected hidden>Status</option>
                <option value="resolved">Resolved</option>
                <option value="active">Not yet resolved</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-info-subtle">
          <button class="btn btn-primary me-1" type="submit">Update Report</button>
          <button class="btn btn-danger" type="button"  data-bs-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>