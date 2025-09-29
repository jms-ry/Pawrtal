<template>
  <div class="modal fade me-2" id="setInspectionScheduleModal" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="setInspectionScheduleModalLabel">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-body border-0 bg-info-subtle">
          <h3 class="fs-5 fw-bolder mt-2 text-uppercase font-monospace">Set Inspection Schedule</h3>
          <div class="card border-0 bg-info-subtle">
            <hr class="text-dark mt-3 mb-2">
            <h6 class="fw-bolder text-uppercase font-monospace mt-2">Preferred Inspection Dates:</h6>
            <div class="row g-2">
              <span class="col-md-5 col-12 mt-2 ms-2 me-4">From:  <strong class="ms-1">{{ inspectionStartDate }}</strong></span>
              <span class="col-md-5 col-12 mt-2 ms-2 me-4">Up to: <strong class="ms-1">{{ inspectionEndDate }}</strong> </span>
            </div>
            <hr class="text-dark mt-3 mb-2">
            <h6 class="fw-bolder text-uppercase font-monospace mt-2">Inspection Details:</h6>
            <form @submit.prevent="submitForm">
              <input type="hidden" name="application_id" class="form-control" v-model="form.application_id">
              <div class="form-floating">
                <input type="text" name="inspection_location" class="form-control" placeholder="Inspection_location" aria-label="Inspection_location" id="floating_inspection_location" @blur="validateLocation" :class="locationValidationClass" v-model="form.inspection_location">
                <label for="floating_inspection_location" class="form-label fw-bold">Inspection Address</label>
                <small class="invalid-feedback fw-bold">{{ addressErrorMessage }}</small>
              </div>

              <div class="row g-2 mt-2">
                <div class="col-12 col-md-6 form-floating">
                  <select name="inspector_id" id="floating_inspector_id" class="form-select" aria-label="size-select" @blur="validateInspector" :class="inspectorValidationClass" v-model="form.inspector_id" >
                    <option selected hidden value="">Inspectors</option>
                    <option v-for="inspector in inspectors" :key="inspector.id" :value="inspector.id">
                      {{ inspector.first_name }}
                    </option>
                  </select>
                  <label for="floating_inspector_id" class="form-label fw-bold">Assign Inspector</label>
                  <small class="invalid-feedback fw-bold">{{ inspectorErrorMessage }}</small>
                </div>
                 <div class="col-12 col-md-6 form-floating">
                  <input type="date" name="inspection_date" class="form-control" placeholder="Inspection date" aria-label="Inspection seen date" id="floating_inspection_date" @blur="validateDate" :class="dateValidationClass" v-model="form.inspection_date">
                  <label for="floating_inspection_date" class="form-label fw-bold">Assign Inspection Date</label>
                  <small class="invalid-feedback fw-bold">{{ dateErrorMessage }}</small>
                 </div>
              </div>

              <hr class="text-dark mt-3 mb-2">

              <div class="modal-footer border-0 bg-info-subtle">
                <button type="submit" class="btn btn-success me-2">Set Inspection</button>
                <button type="button" class="btn btn-danger ms-1" @click="closeModal">Close</button>
              </div>
            </form>
            
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { useForm } from '@inertiajs/vue3'
  import { ref, onMounted, computed} from 'vue'
  import { Modal } from 'bootstrap'

  const inspectionStartDate = ref(null)
  const inspectionEndDate = ref(null)
  const applicationId = ref(null)
  const fullAddress = ref(null)
  const startDate = ref(null)
  const endDate = ref(null)

  const props = defineProps({
    inspectors: Object
  })

  const form = useForm({
    application_id: '',
    inspection_location: '',
    inspector_id:'',
    inspection_date:''
  })

  function initializeForm(){
    form.application_id = applicationId.value || ''
    form.inspection_location = fullAddress.value || ''
  }

  onMounted(() => {
    const setInspectionModal = document.getElementById('setInspectionScheduleModal');

    setInspectionModal.addEventListener('show.bs.modal',(event) => {
      const button = event.relatedTarget;

      applicationId.value = button.getAttribute('data-application-id')
      inspectionStartDate.value = button.getAttribute('data-application-start-date')
      inspectionEndDate.value = button.getAttribute('data-application-end-date')
      fullAddress.value = button.getAttribute('data-application-address')

      startDate.value = new Date(inspectionStartDate.value)
      endDate.value = new Date(inspectionEndDate.value)

      initializeForm()
    })
  })

  const addressErrorMessage = ref(null)
  const addressIsValid = ref(null)

  const locationValidationClass = computed(() => {
    if(addressIsValid.value === true) return 'is-valid'
    if(addressIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateLocation(){
    const location = form.inspection_location.trim()
    const regex = /^[\p{L}0-9\s.,#'"\-()/]{5,100}$/u

    if(!location){
      addressIsValid.value = false
      addressErrorMessage.value = "Inspection Location is required."
      return false
    }

    if (!regex.test(location)) {
      addressIsValid.value= false
      addressErrorMessage.value  = "Enter a valid location (letters, numbers, spaces, and basic punctuation, min 5 characters)."
      return false
    }

    addressIsValid.value = true
    addressErrorMessage.value = ""
    return true
    
  }

  const inspectorErrorMessage = ref(null)
  const inspectorIsValid = ref(null)

  const inspectorValidationClass = computed(() => {
    if(inspectorIsValid.value === true) return 'is-valid'
    if(inspectorIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateInspector(){
    const inspector = form.inspector_id

    if(!inspector){
      inspectorIsValid.value = false
      inspectorErrorMessage.value = "Please assign an inspector."
      return false;
    }

    inspectorIsValid.value = true
    inspectorErrorMessage.value = ''
    return true
  }

  const dateErrorMessage = ref(null)
  const dateIsValid = ref(null)

  const dateValidationClass = computed(() => {
    if(dateIsValid.value === true) return 'is-valid'
    if(dateIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateDate(){
    const date = form.inspection_date

    if(!date){
      dateIsValid.value = false
      dateErrorMessage.value = "Inspection Date is required."
      return false
    }

    const selectedDate = new Date(date)

    if (!startDate.value || !endDate.value) {
      dateIsValid.value = false
      dateErrorMessage.value = "Inspection period is not set."
      return false
    }

    if (selectedDate < startDate.value || selectedDate > endDate.value) {
      dateIsValid.value = false
      dateErrorMessage.value = `Date must be between ${startDate.value.toLocaleDateString('en-US',{ month: 'short', day: '2-digit',year: 'numeric'})} and ${endDate.value.toLocaleDateString('en-US',{ month: 'short', day: '2-digit',year: 'numeric'})}.`
      return false
    }

    dateIsValid.value = true
    dateErrorMessage.value = ""
    return true
  }

  function submitForm(){
    const formData = new FormData()

    formData.append('application_id', form.application_id)
    formData.append('inspector_id', form.inspector_id)
    formData.append('inspection_location', form.inspection_location)
    formData.append('inspection_date', form.inspection_date)

    const isLocationValid = validateLocation()
    const isInspectorValid = validateInspector()
    const isDateValid = validateDate()

    if(!isLocationValid || !isInspectorValid || !isDateValid){
      return
    }

    form.post(`/inspection-schedules`,{
      data: formData,
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
    const modalEl = document.getElementById('setInspectionScheduleModal')
    const modal = Modal.getInstance(modalEl)
    if (modal) {
      modal.hide()
    }

    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove())
    document.body.classList.remove('modal-open')
    document.body.style.removeProperty('overflow')
    document.body.style.removeProperty('padding-right')
   
    addressIsValid.value = null
    addressErrorMessage.value = ''

    inspectorIsValid.value = null
    inspectorErrorMessage.value = ''

    dateIsValid.value = null
    dateErrorMessage.value = ''
    
  }
</script>