<template>
  <div class="modal fade me-2" id="adoptionApplicationFormModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="adoptionApplicationFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-sm-scrollable modal-lg">
      <div class="modal-content ">
        <div class="modal-header bg-info-subtle font-monospace">
          <i class="bi bi-house-add-fill me-2 text-primary fs-2"></i>
          <h5 class="modal-title">Submit Adoption Application for <strong id="adoption_form_adoptable-name">Rescue Name</strong></h5>
        </div>
        <form @submit="submitForm">
          <div class="modal-body bg-info-subtle border-0">
            <input type="hidden" name="user_id" class="form-control" id="adoption_form_user_id">
            <input type="hidden" name="rescue_id" class="form-control" id="adoption_form_rescue_id">
            <div class="row g-2 mt-2">
              <div class="col-12 col-md-6 form-floating">
                <input type="date" name="preferred_inspection_start_date" class="form-control" placeholder="Inspection Start Date" aria-label="Inspection Start Date" id="floating_preferred_inspection_start_date" required>
                <label for="floating_preferred_inspection_start_date" class="form-label fw-bold">Inspection Start Date</label>
              </div>
              <div class="col-12 col-md-6 form-floating">
                <input type="date" name="preferred_inspection_end_date" class="form-control" placeholder="Inspection End Date" aria-label="Inspection End Date" id="floating_preferred_inspection_end_date" required>
                <label for="floating_preferred_inspection_end_date" class="form-label fw-bold">Inspection End Date</label>
              </div>
            </div>

            <div class="row g-2 mt-3">
              <div class="col-12 col-md-6">
                <label for="valid_id" class="form-label fw-bold">Upload Valid ID</label>
                <input type="file" name="valid_id" id="valid_id" class="form-control" accept="image/*,.pdf,.doc,.docx" required>
              </div>
              <div class="col-12 col-md-6">
                <label for="supporting_documents" class="form-label fw-bold">Upload Supporting Documents</label>
                <input type="file" name="supporting_documents[]" id="supporting_documents" class="form-control" accept="image/*,.pdf,.doc,.docx" multiple required>
              </div>
            </div>

            <div class="row g-2 mt-3">
              <div class="col-12 form-floating">
                <textarea name="reason_for_adoption" id="floating_reason_for_adoption" class="form-control" placeholder="Reason for adoption" aria-label="Reason for adoption" style="height: 100px" required></textarea>
                <label for="floating_reason_for_adoption" class="form-label fw-bold">State your reason for adoption</label>
              </div>
            </div>
          </div>
          <div class="modal-footer bg-info-subtle">
            <button class="btn btn-primary me-1" type="submit" >Submit Application</button>
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

  function submitForm(event) {
    event.preventDefault()
    const formData = new FormData(event.target)

    router.post('/adoption-applications', formData, {
      forceFormData: true,
      preserveScroll: false,
      preserveState: false,
      onSuccess: () => {
        closeModal()
      },

      onError: (errors) => {
        console.error("Validation errors:", errors)
      }
    })
  }

  function closeModal(){
    const modalEl = document.getElementById('adoptionApplicationFormModal')
    const modal = Modal.getInstance(modalEl)
    if (modal) {
      modal.hide()
    }

    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove())
    document.body.classList.remove('modal-open')
    document.body.style.removeProperty('overflow')
    document.body.style.removeProperty('padding-right')
    form.reset()
  }
</script>