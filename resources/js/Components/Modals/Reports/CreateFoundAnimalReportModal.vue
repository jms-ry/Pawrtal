<template>
  <div class="modal fade" id="createFoundAnimalReportModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="createFoundAnimalReportModalLabel">
  <div class="modal-dialog modal-dialog-centered modal-dialog-sm-scrollable modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-info-subtle">
        <i class="bi bi-plus-circle-fill me-3 text-primary fs-2"></i>
        <h5 class="modal-title">Create a New Found Animal Report!</h5>
      </div>
      <form  @submit="submitForm">
        <div class="modal-body bg-info-subtle border-0">
          <input type="hidden" name="type" class="form-control" value="found">
          <input type="hidden" name="user_id" class="form-control" :value="user?.id">
          <div class="row g-2 mt-2">
            <div class="col-12 col-md-4 form-floating">
              <input type="text" name="species" class="form-control" placeholder="Animal species" aria-label="Animal species" id="floating_animal_species" autocomplete="true" required>
              <label for="floating_animal_species" class="form-label fw-bold">Species (e.g Dog, Cat, etc.)</label>
            </div>
            <div class="col-12 col-md-4 form-floating">
              <input type="text" name="breed" class="form-control" placeholder="Animal breed" aria-label="Animal breed" id="floating_animal_breed" autocomplete="true" required>
              <label for="floating_animal_breed" class="form-label fw-bold">Breed</label>
            </div>
            <div class="col-12 col-md-4 form-floating">
              <input type="text" name="color" class="form-control" placeholder="Animal color" aria-label="Animal color" id="floating_animal_color" autocomplete="true" required>
              <label for="floating_animal_color" class="form-label fw-bold">Color</label>
            </div>
          </div>

          <div class="row g-2 mt-2">
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
            <div class="col-12 col-md-4 form-floating">
              <select name="size" id="floating_animal_size" class="form-select" aria-label="size-select" required>
                <option selected hidden>Size</option>
                <option value="small">Small</option>
                <option value="medium">Medium</option>
                <option value="large">Large</option>
              </select>
              <label for="floating_animal_size" class="form-label fw-bold">Select size</label>
            </div>
          </div>

          <div class="row g-2 mt-2">
            <div class="col-12 col-md-4 form-floating">
              <input type="text" name="distinctive_features" class="form-control" placeholder="Animal distinctive features" aria-label="Animal distinctive features" id="floating_animal_distinctive_features">
              <label for="floating_animal_distinctive_features" class="form-label fw-bold">Distinctive Features</label>
            </div>
            <div class="col-12 col-md-4 form-floating">
              <input type="text" name="condition" class="form-control" placeholder="Animal condition" aria-label="Animal condition" id="floating_animal_condition">
              <label for="floating_animal_distinctive_features" class="form-label fw-bold">Condition</label>
            </div>
            <div class="col-12 col-md-4 form-floating">
              <input type="text" name="temporary_shelter" class="form-control" placeholder="Animal temporary" aria-label="Animal temporary shelter" id="floating_animal_temporary_shelter">
              <label for="floating_animal_temporary_shelter" class="form-label fw-bold">Temporary Shelter</label>
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
</template>

<script setup>
  import { router } from '@inertiajs/vue3'
  import { Modal } from 'bootstrap'

  const props = defineProps({
    user: {
      type: Object,
      default: () => null
    }
  })

  function submitForm(event) {
    event.preventDefault()
    const form = event.target
    const formData = new FormData(event.target)

    router.post('/reports', formData, {
      forceFormData: true,
      preserveScroll: true,
      preserveState: false,
      onSuccess:() => {
        closeModal()
        form.reset()
      },
    })
  }

  function closeModal(){
    const modalEl = document.getElementById('createFoundAnimalReportModal')
    const modal = Modal.getInstance(modalEl)
    if (modal) {
      modal.hide()
    }

    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove())
    document.body.classList.remove('modal-open')
    document.body.style.removeProperty('overflow')
    document.body.style.removeProperty('padding-right')
  }
</script>
