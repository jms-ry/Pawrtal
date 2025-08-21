<div class="modal fade" id="createLostAnimalReportModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="createLostAnimalReportModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-sm-scrollable modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-info-subtle">
        <i class="bi bi-plus-circle-fill me-3 text-primary fs-2"></i>
        <h5 class="modal-title">Create a New Lost Animal Report!</h5>
      </div>
      <form action="{{ route('reports.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body bg-info-subtle border-0">
          <input type="hidden" name="type" class="form-control" value="lost">
          <input type="hidden" name="user_id" class="form-control" value="{{ $user->id }}">
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
              <select name="sex" id="floating_sex" class="form-select" aria-label="sex-select" required>
                <option selected hidden>Sex</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="unknown">Unknown</option>
              </select>
              <label for="floating_sex" class="form-label fw-bold">Select a sex</label>
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
              <label for="floating_animal_last_seen_date fw-bold" class="form-label">Last Seen Date</label>
            </div>
          </div>

          <div class="row g-2 mt-2">
            <div class="col-12">
              <label for="image" class="form-label fw-bold">Upload an Image</label>
              <input type="file" name="image" id="image" class="form-control" accept="image/*" required>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-info-subtle">
          <button class="btn btn-primary me-1" type="submit">Submit Report</button>
          <button class="btn btn-danger" type="button"  data-bs-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>