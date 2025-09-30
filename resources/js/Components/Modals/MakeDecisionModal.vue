<template>
  <div class="modal fade me-2" id="makeDecisionModal" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="makeDecisionModalLabel">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-body border-0 bg-info-subtle">
          <h3 class="fs-5 fw-bolder mt-2 text-uppercase text-center font-monospace">Make A Decision</h3>
          <div class="card border-0 bg-info-subtle">
            <hr class="text-dark mt-3 mb-2">
            
            <form @submit.prevent="submitForm">
              <!-- Status Radio Buttons (Horizontal) -->
              <div class="mb-3">
                <label class="form-label fw-semibold">Decision Status</label>
                <div class="d-flex gap-3">
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="status" id="statusApproved" value="approved" @change="validateStatus" v-model="form.status">
                    <label class="form-check-label" for="statusApproved">
                      Approve this application
                    </label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="status" id="statusRejected" value="rejected" @change="validateStatus" v-model="form.status">
                    <label class="form-check-label" for="statusRejected">
                      Reject this application
                    </label>
                  </div>
                </div>
                <div v-if="statusErrorMessage" class="invalid-feedback d-block">
                  {{ statusErrorMessage }}
                </div>
              </div>

              <!-- Review Notes Textarea (Floating) -->
              <div class="form-floating mb-3">
                <textarea class="form-control" id="reviewNotes" name="review_notes" placeholder="Enter your review comments..." style="height: 100px" @blur="validateReviewNotes" :class="notesValidationClass" v-model="form.review_notes"></textarea>
                <label for="reviewNotes">Review Notes</label>
                <div v-if="notesErrorMessage" class="invalid-feedback d-block">
                  {{ notesErrorMessage }}
                </div>
              </div>

              <!-- Hidden Reviewed By Field -->
              <input type="hidden" name="reviewed_by" v-model="form.reviewed_by">
              <input type="hidden" name="review_date" v-model="form.review_date">

              <!-- Submit Button -->
              <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Submit Decision</button>
                <button type="button" class="btn btn-secondary" @click="closeModal">Cancel</button>
              </div>
            </form>
            
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { useForm } from '@inertiajs/vue3';
  import { computed, onMounted, ref } from 'vue';
  import { Modal } from 'bootstrap'
  
  const props = defineProps({
    user: {
      type: Object,
    }
  })

  const applicationId = ref(null)

  const form = useForm({
    status:'',
    reviewed_by:'',
    review_date:'',
    review_notes:'',
    _method:'PUT'

  })

  function initializeForm(){
    form.reviewed_by = props.user?.fullName || ''
    form.review_date = new Date().toISOString().split('T')[0]
  }
  onMounted(() => {
    const makeDecisionModal = document.getElementById('makeDecisionModal');

    makeDecisionModal.addEventListener('show.bs.modal',(event) => {
      const button = event.relatedTarget;

      applicationId.value = button.getAttribute('data-application-id')

      initializeForm()
    })
  })

  const statusErrorMessage = ref(null)
  function validateStatus(){
    const status = form.status

    if(!status){
      statusErrorMessage.value = "Please make a choice."
      return false
    }

    statusErrorMessage.value = ''
    return true
  }

  const notesErrorMessage = ref(null)
  const notesIsValid = ref(null)

  const notesValidationClass = computed(() => {
    if(notesIsValid.value === true) return 'is-valid'
    if(notesIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateReviewNotes(){
    const notes = form.review_notes.trim()
    const regex = /^[A-Z].+[.!?]$/
    if(!notes){
      notesIsValid.value = false
      notesErrorMessage.value = "Please leave a review notes."
      return false
    }

    if(!regex.test(notes)){
      notesIsValid.value = false
      notesErrorMessage.value = "Please leave review notes in sentence form."
      return false
    }

    notesIsValid.value = true
    notesErrorMessage.value = ''
    return true
  }

  function submitForm(){

    const isNotesValid = validateReviewNotes()
    const isStatusValid = validateStatus()

    if(!isNotesValid || !isStatusValid){
      return
    }
    
    form.put(`/adoption-applications/${applicationId.value}`,{
      preserveScroll: false,
      preserveState: false,
      onSuccess: () => {
        closeModal()
     },
    })
  }
  function closeModal(){
    const modalEl = document.getElementById('makeDecisionModal')
    const modal = Modal.getInstance(modalEl)
    if (modal) {
      modal.hide()
    }

    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove())
    document.body.classList.remove('modal-open')
    document.body.style.removeProperty('overflow')
    document.body.style.removeProperty('padding-right')
    
    applicationId.value = null

    statusErrorMessage.value = null
    notesErrorMessage.value = null
    notesIsValid.value = null
  }
</script>