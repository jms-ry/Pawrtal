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
                <input type="date" name="preferred_inspection_start_date" :class="startDateValidationClass" @blur="validateStartDate" v-model="startDateValue" class="form-control" placeholder="Inspection Start Date" aria-label="Inspection Start Date" id="floating_preferred_inspection_start_date">
                <label for="floating_preferred_inspection_start_date" class="form-label fw-bold">Inspection Start Date</label>
                <small class="invalid-feedback fw-bold">{{ startDateErrorMessage }}</small>
              </div>
              <div class="col-12 col-md-6 form-floating">
                <input type="date" name="preferred_inspection_end_date" :class="endDateValidationClass" @blur="validateEndDate" v-model="endDateValue" class="form-control" placeholder="Inspection End Date" aria-label="Inspection End Date" id="floating_preferred_inspection_end_date">
                <label for="floating_preferred_inspection_end_date" class="form-label fw-bold">Inspection End Date</label>
                <small class="invalid-feedback fw-bold">{{ endDateErrorMessage }}</small>
              </div>
            </div>

            <div class="row g-2 mt-3">
              <div class="col-12 col-md-6">
                <label for="valid_id" class="form-label fw-bold">Upload Valid ID</label>
                <input type="file" name="valid_id" id="valid_id" class="form-control" accept="image/*,.pdf,.doc,.docx" @change="validateValidID">
                <small class="invalid-feedback fw-bold">{{ validIdErrorMessage }}</small>
              </div>
              <div class="col-12 col-md-6">
                <label for="supporting_documents" class="form-label fw-bold">Upload Supporting Documents</label>
                <input type="file" name="supporting_documents[]" id="supporting_documents" class="form-control" accept="image/*,.pdf,.doc,.docx" multiple @change="validateSupportingDocuments">
                <small class="invalid-feedback fw-bold">{{ supportingDocumentsErrorMessage }}</small>
              </div>
            </div>

            <div class="row g-2 mt-3">
              <div class="col-12 form-floating">
                <textarea name="reason_for_adoption" id="floating_reason_for_adoption" :class="reasonForAdoptionValidationClass" @blur="validateReasonForAdoption" v-model="reasonForAdoptionValue" class="form-control" placeholder="Reason for adoption" aria-label="Reason for adoption" style="height: 100px"></textarea>
                <label for="floating_reason_for_adoption" class="form-label fw-bold">State your reason for adoption</label>
                <small class="invalid-feedback fw-bold">{{ reasonForAdoptionErrorMessage }}</small>
              </div>
            </div>
          </div>
          <div class="modal-footer bg-info-subtle">
            <button class="btn btn-primary me-1" type="submit" >Submit Application</button>
            <button class="btn btn-danger" type="button" @click="closeModal()">Close</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { router } from '@inertiajs/vue3'
  import { Modal } from 'bootstrap'
  import { ref, computed } from 'vue'

  const startDateValue = ref('')
  const startDateIsValid = ref(null)
  const startDateErrorMessage = ref('')

  const startDateValidationClass = computed(() => {
    if(startDateIsValid.value === true) return 'is-valid'
    if(startDateIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateStartDate(){
    const start_date = startDateValue.value

    if(!start_date){
      startDateIsValid.value = false
      startDateErrorMessage.value = "Inspection Start Date is required."
      return false
    }

    const selectedStartDate = new Date(start_date)
    const today = new Date()
    today.setHours(0,0,0,0)

    if(selectedStartDate < today){
      startDateIsValid.value = false
      startDateErrorMessage.value = "Start Date can't be in the past."
      return false
    }

    startDateIsValid.value = true
    startDateErrorMessage.value = ""
    return true
  }

  const endDateValue = ref('')
  const endDateIsValid = ref(null)
  const endDateErrorMessage = ref('')

  const endDateValidationClass = computed(() => {
    if(endDateIsValid.value === true) return 'is-valid'
    if(endDateIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateEndDate(){
    const end_date = endDateValue.value
    const start_date = startDateValue.value

    if(!end_date){
      endDateIsValid.value = false
      endDateErrorMessage.value = "Inspection End Date is required."
      return false
    }

    const selectedEndDate = new Date(end_date)
    const today = new Date()
    today.setHours(0,0,0,0)
    const selectedStartDate = new Date(start_date)

    if(selectedEndDate < today){
      endDateIsValid.value = false
      endDateErrorMessage.value = "End Date can't be in the past."
      return false
    }

    if(selectedEndDate < selectedStartDate){
      endDateIsValid.value = false
      endDateErrorMessage.value = "End Date must be later than or the same with the start date."
      return false
    }

    endDateIsValid.value = true
    endDateErrorMessage.value = ""
    return true
  }

  const validIdErrorMessage = ref("")

  function validateValidID(event = null) {
    const input = event ? event.target : document.getElementById("valid_id")
    const file = input.files[0]

    if (!file) {
      validIdErrorMessage.value = "Valid ID is required."
      input.classList.add('is-invalid')
      return false
    }

    const allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf']
    const fileExtension = file.name.split('.').pop().toLowerCase()
    if (!allowedExtensions.includes(fileExtension)) {
      input.classList.add('is-invalid')
      validIdErrorMessage.value = "Only JPG, JPEG, PNG, and PDF are allowed."
      return false
    }

    if (file.size > 2 * 1024 * 1024) {
      validIdErrorMessage.value = "File must not exceed 2MB."
      input.classList.add('is-invalid')
      return false
    }
    
    validIdErrorMessage.value = ""
    input.classList.remove('is-invalid')
    input.classList.add('is-valid')
    return true
  }

  const supportingDocumentsErrorMessage = ref("")

  function validateSupportingDocuments(event = null) {
    const input = event ? event.target : document.getElementById("supporting_documents")
    const files = input.files

    if (!files.length) {
      supportingDocumentsErrorMessage.value = "This field is required."
      input.classList.add("is-invalid")
      return false
    }

    const allowedTypes = ["image/jpeg", "image/png", "image/jpg", "application/pdf"]
    for (let i = 0; i < files.length; i++) {
      const file = files[i]

      if (!allowedTypes.includes(file.type)) {
        supportingDocumentsErrorMessage.value = `File "${file.name}" is not a valid format. Only JPEG, JPG, PNG, and PDF allowed.`
        input.classList.add("is-invalid")
        input.classList.remove("is-valid")
        return false
      }

      if (file.size > 2 * 1024 * 1024) {
        supportingDocumentsErrorMessage.value = `File "${file.name}" exceeds 2MB size limit.`
        input.classList.add("is-invalid")
        input.classList.remove("is-valid")
        return false
      }
    }

    supportingDocumentsErrorMessage.value = ""
    input.classList.remove("is-invalid")
    input.classList.add("is-valid")
    return true
  }


  const reasonForAdoptionValue = ref('')
  const reasonForAdoptionIsValid = ref(null) 
  const reasonForAdoptionErrorMessage = ref('')

  const reasonForAdoptionValidationClass = computed(() => {
    if (reasonForAdoptionIsValid.value === true) return 'is-valid'
    if (reasonForAdoptionIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateReasonForAdoption() {
    const reasonForAdoption = reasonForAdoptionValue.value.trim()

    if(!reasonForAdoption){
      reasonForAdoptionIsValid.value = false
      reasonForAdoptionErrorMessage.value = 'Reason For Adoption is required'
      return false
    }

    if (reasonForAdoption.length < 2) {
      reasonForAdoptionIsValid.value = false
      reasonForAdoptionErrorMessage.value = 'Reason For Adoption must be at least 2 characters long'
      return false
    }
    
    const sentences = reasonForAdoption.split(/(?<=[.!?])\s+/)
    for (const sentence of sentences) {
      const trimmed = sentence.trim()
      if (trimmed && /^\d/.test(trimmed)) {
        reasonForAdoptionIsValid.value = false
        reasonForAdoptionErrorMessage.value = 'Each sentence must not start with a number'
        return false
      }
    }
    
    reasonForAdoptionIsValid.value = true
    reasonForAdoptionErrorMessage.value = ''
    return true
  }
  function submitForm(event) {
    event.preventDefault()
    const formData = new FormData(event.target)

    const isStartDateValid = validateStartDate()
    const isEndDateValid = validateEndDate()
    const isReasonForAdoptionValid = validateReasonForAdoption()
    const isValidIDValid = validateValidID()
    const isSupportingDocumentsValid = validateSupportingDocuments()

    if(!isStartDateValid || !isEndDateValid || !isValidIDValid || !isSupportingDocumentsValid || !isReasonForAdoptionValid){
      return
    }
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

    startDateValue.value = ''
    startDateErrorMessage.value = ''
    startDateIsValid.value = null

    endDateValue.value = ''
    endDateErrorMessage.value = ''
    endDateIsValid.value = null

    validIdErrorMessage.value = null
    const fileInput = document.getElementById('valid_id')
    if (fileInput) {
      fileInput.value = ''
      fileInput.classList.remove('is-valid', 'is-invalid')
    }

    supportingDocumentsErrorMessage.value = ''
    const imageInput = document.getElementById('supporting_documents')
    if (imageInput) {
      imageInput.value = ''
      imageInput.classList.remove('is-valid', 'is-invalid')
    }

    reasonForAdoptionValue.value = ''
    reasonForAdoptionErrorMessage.value = ''
    reasonForAdoptionIsValid.value = null
  }
</script>