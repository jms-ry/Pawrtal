<template>
  <div class="modal fade" id="updateFoundReportModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="updateReportModalLabel">
    <div class="modal-dialog modal-dialog-centered modal-dialog-sm-scrollable modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-info-subtle">
          <i class="bi bi-plus-circle-fill me-3 text-primary fs-2"></i>
          <h5 class="modal-title">Update <span id="found-modal-title-span"></span> Report!</h5>
        </div>
        <!--Found Animal Report Form-->
        <form @submit.prevent="submitForm">
          <div class="modal-body bg-info-subtle border-0">
            <input type="hidden" name="user_id" class="form-control" v-model="form.user_id">
            <div class="row g-2 mt-2">
              <div class="col-12 col-md-4 form-floating">
                <input type="text" v-model="form.species" name="species" class="form-control" placeholder="Animal species" aria-label="Animal species" id="floating_animal_species_found" autocomplete="true" :class="speciesValidationClass" @blur="validateSpecies">
                <label for="floating_animal_species_found" class="form-label fw-bold">Species (e.g Dog, Cat, etc.)</label>
                <small class="invalid-feedback fw-bold">{{ speciesErrorMessage }}</small>
              </div>
              <div class="col-12 col-md-4 form-floating">
                <input type="text" name="breed" v-model="form.breed" class="form-control" placeholder="Animal breed" aria-label="Animal breed" id="floating_animal_breed_found" autocomplete="true" :class="breedValidationClass" @blur="validateBreed">
                <label for="floating_animal_breed_found" class="form-label fw-bold">Breed</label>
                <small class="invalid-feedback fw-bold">{{ breedErrorMessage }}</small>
              </div>
              <div class="col-12 col-md-4 form-floating">
                <input type="text" name="color" v-model="form.color" class="form-control" placeholder="Animal color" aria-label="Animal color" id="floating_animal_color_found" autocomplete="true" :class="colorValidationClass" @blur="validateColor">
                <label for="floating_animal_color_found" class="form-label fw-bold">Color</label>
                <small class="invalid-feedback fw-bold">{{ colorErrorMessage }}</small>
              </div>
            </div>

            <div class="row g-2 mt-2">
              <div class="col-12 col-md-4 form-floating">
                <select name="sex" v-model="form.sex" id="floating_animal_sex_found" class="form-select" aria-label="sex-select" :class="sexValidationClass" @blur="validateSex">
                  <option selected hidden value="">Sex</option>
                  <option value="male">Male</option>
                  <option value="female">Female</option>
                  <option value="unknown">Unknown</option>
                </select>
                <label for="floating_animal_sex_found" class="form-label fw-bold">Select a sex</label>
                <small class="invalid-feedback fw-bold">{{ sexErrorMessage }}</small>
              </div>
              <div class="col-12 col-md-4 form-floating">
                <input type="text" name="age_estimate" v-model="form.age_estimate" class="form-control" placeholder="Animal age estimate" aria-label="Animal age estimate" id="floating_animal_age_estimate_found" autocomplete="true" :class="ageValidationClass" @blur="validateAge">
                <label for="floating_animal_age_estimate_found" class="form-label fw-bold">Age Estimate (e.g 6 months old)</label>
                <small class="invalid-feedback fw-bold">{{ ageErrorMessage }}</small>
              </div>
              <div class="col-12 col-md-4 form-floating">
                <select name="size" id="floating_animal_size_found" v-model="form.size" class="form-select" aria-label="size-select" :class="sizeValidationClass" @blur="validateSize">
                  <option selected hidden value="">Size</option>
                  <option value="small">Small</option>
                  <option value="medium">Medium</option>
                  <option value="large">Large</option>
                </select>
                <label for="floating_animal_size_found" class="form-label fw-bold">Select size</label>
                <small class="invalid-feedback fw-bold">{{ sizeErrorMessage }}</small>
              </div>
            </div>

            <div class="row g-2 mt-2">
              <div class="col-12 col-md-4 form-floating">
                <input type="text" name="distinctive_features" v-model="form.distinctive_features" class="form-control" placeholder="Animal distinctive features" aria-label="Animal distinctive features" id="floating_animal_distinctive_features_found" :class="distinctiveFeaturesValidationClass" @blur="validateDistinctiveFeatures">
                <label for="floating_animal_distinctive_features_found" class="form-label fw-bold">Distinctive Features</label>
                <small class="invalid-feedback fw-bold">{{ distinctiveFeaturesErrorMessage }}</small>
                <small class="valid-feedback text-dark fw-light">{{ distinctiveFeaturesErrorMessage }}</small>
              </div>
              <div class="col-12 col-md-4 form-floating">
                <input type="text" name="condition" v-model="form.condition" :class="conditionValidationClass" @blur="validateCondition" class="form-control" placeholder="Animal condition" aria-label="Animal condition" id="floating_animal_condition_found">
                <label for="floating_animal_condition_found" class="form-label fw-bold">Condition</label>
                <small class="invalid-feedback fw-bold">{{ conditionErrorMessage }}</small>
                <small class="valid-feedback text-dark">{{ conditionErrorMessage }}</small>
              </div>
              <div class="col-12 col-md-4 form-floating">
                <input type="text" name="temporary_shelter" v-model="form.temporary_shelter" class="form-control" placeholder="Animal temporary" aria-label="Animal temporary shelter" id="floating_animal_temporary_shelter_found" :class="shelterValidationClass" @blur="validateShelter">
                <label for="floating_animal_temporary_shelter_found" class="form-label fw-bold">Temporary Shelter</label>
                <small class="invalid-feedback fw-bold">{{ shelterErrorMessage }}</small>
                <small class="valid-feedback text-dark">{{ shelterErrorMessage }}</small>
              </div>
            </div>

            <div class="row g-2 mt-2">
              <div class="col-12 col-md-8 form-floating">
                <input type="text" name="found_location" v-model="form.found_location" class="form-control" placeholder="Animal found location" aria-label="Animal found location" id="floating_animal_found_location" :class="locationValidationClass" @blur="validateLocation">
                <label for="floating_animal_found_location" class="form-label fw-bold">Found Location</label>
                <small class="invalid-feedback fw-bold">{{ locationErrorMessage }}</small>
              </div>
              <div class="col-12 col-md-4 form-floating">
                <input type="date" name="found_date" v-model="form.found_date" class="form-control" placeholder="Animal last seen date" aria-label="Animal found date" id="floating_animal_found_date" :class="dateValidationClass" @blur="validateDate">
                <label for="floating_animal_found_date" class="form-label fw-bold">Found Date</label>
                <small class="invalid-feedback fw-bold">{{ dateErrorMessage }}</small>
              </div>
            </div>

            <div class="row g-2 mt-2">
              <div class="col-12 col-md-8">
                <label for="found_image" class="form-label fw-bold">Update Image</label>
                <input type="file" name="image" id="found_image" class="form-control" accept="image/*" @change="handleImageChange" :class="{'is-invalid': imageErrorMessage}">
                <small class="invalid-feedback fw-bold">{{ imageErrorMessage }}</small>
                <div v-if="!imageErrorMessage">
                  <small class="text-muted mt-3">Leave blank to keep existing image</small>
                  <div class="mb-2 mt-2">
                    <img id="reportImageFound" class="w-100 h-100 object-fit-cover rounded-4" style="max-height: 150px;">
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-4">
                <label for="report_status_found" class="form-label fw-bold">Update Report Status</label>
                <select name="status" v-model="form.status" id="report_status_found" class="form-select" aria-label="size-select":class="statusValidationClass" @blur="validateStatus">
                  <option selected hidden value="">Status</option>
                  <option value="resolved">Resolved</option>
                  <option value="active">Not yet resolved</option>
                </select>
                <small class="invalid-feedback fw-bold">{{ statusErrorMessage }}</small>
              </div>
            </div>
          </div>
          <div class="modal-footer bg-info-subtle">
            <button class="btn btn-primary me-1" type="submit">Update Report</button>
            <button class="btn btn-danger" type="button" @click="closeModal">Close</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { useForm } from '@inertiajs/vue3'
  import { Modal } from 'bootstrap'
  import { ref, onMounted , computed} from 'vue'

  const props = defineProps({
    user: {
      type: Object,
    },
  })

  const reportId = ref(null)
  const reportAnimalSpecies = ref(null)
  const reportAnimalBreed = ref(null)
  const reportAnimalColor = ref(null)
  const reportAnimalSex = ref(null)
  const reportAnimalAgeEstimate = ref(null)
  const reportAnimalSize = ref(null)
  const reportAnimalDistinctiveFeatures = ref(null)
  const reportAnimalFoundLocation = ref(null)
  const reportAnimalFoundDate = ref(null)
  const reportAnimalStatus = ref(null)
  const reportAnimalCondition = ref(null)
  const reportAnimalTemporaryShelter = ref(null)

  const form = useForm({
    species: '',
    breed: '',
    color:'',
    sex: '',
    age_estimate:'',
    size:'',
    distinctive_features:'',
    found_location: '',
    found_date:'',
    status:'',
    condition:'',
    temporary_shelter:'',
    image:null,
    type:'found',
    user_id:'',
    _method:'PUT'
  })

  function initializeForm(){
    form.species = reportAnimalSpecies || ''
    form.breed = reportAnimalBreed || ''
    form.color = reportAnimalColor || ''
    form.sex = reportAnimalSex || ''
    form.age_estimate = reportAnimalAgeEstimate || ''
    form.size = reportAnimalSize || ''
    form.distinctive_features = reportAnimalDistinctiveFeatures || ''
    form.found_location = reportAnimalFoundLocation || ''
    form.found_date = reportAnimalFoundDate ||''
    form.status = reportAnimalStatus || ''
    form.condition = reportAnimalCondition || ''
    form.temporary_shelter = reportAnimalTemporaryShelter || ''
    form.user_id = props.user?.id || ''
    form.image = null
  }

  onMounted(() => {
    const modalEl = document.getElementById('updateFoundReportModal')
    
    modalEl.addEventListener('show.bs.modal', (event) => {
      const button = event.relatedTarget  
      
      reportId.value = button.getAttribute('data-report-id')


      reportAnimalSpecies.value = button.getAttribute('data-report-species')

      reportAnimalBreed.value = button.getAttribute('data-report-breed')

      reportAnimalColor.value = button.getAttribute('data-report-color')

      reportAnimalSex.value = button.getAttribute('data-report-sex')

      reportAnimalAgeEstimate.value = button.getAttribute('data-report-age-estimate')

      reportAnimalSize.value = button.getAttribute('data-report-size')

      reportAnimalDistinctiveFeatures.value = button.getAttribute('data-report-distinctive-features')

      reportAnimalFoundLocation.value = button.getAttribute('data-report-location')

      reportAnimalFoundDate.value = button.getAttribute('data-report-found-date')

      reportAnimalStatus.value = button.getAttribute('data-report-status')

      reportAnimalCondition.value = button.getAttribute('data-report-condition')

      reportAnimalTemporaryShelter.value = button.getAttribute('data-report-temporary-shelter')

      initializeForm()

    })

    
  })

  const imageErrorMessage = ref('')
  function handleImageChange(event) {
    const file = event.target.files[0]
    const input = document.getElementById('found_image')
    if (!file) {
      input.classList.add('is-invalid')
      imageErrorMessage.value = "Image is required"
      return false
    }

    const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg']
    const maxSize = 2 * 1024 * 1024 // 2MB

    if (!allowedTypes.includes(file.type)) {
      imageErrorMessage.value = 'Only JPG and PNG images are allowed.'
      input.classList.add('is-invalid')
      return false
    }

    if (file.size > maxSize) {
      imageErrorMessage.value = 'Image size must be less than 2MB.'
      input.classList.add('is-invalid')
      return false
    }

    // Passed validation
    form.image = file
    input.classList.remove('is-invalid')
    input.classList.add('is-valid')
    imageErrorMessage.value = ''
    return true
  }

  const  speciesIsValid = ref(null) 
  const  speciesErrorMessage = ref('')

  const speciesValidationClass = computed(() => {
    if (speciesIsValid.value === true) return 'is-valid'
    if (speciesIsValid.value === false) return 'is-invalid'
    return ''
  })

  const statusIsValid = ref(null)
  const statusErrorMessage = ref('')

  const statusValidationClass = computed(() => {
    if(statusIsValid.value === true) return 'is-valid'
    if(statusIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateStatus(){
    const status = form.status

    if(!status){
      statusErrorMessage.value = "Status is required."
      statusIsValid.value = false
      return false
    }
    
    statusIsValid.value = true
    statusErrorMessage.value = ''
    return true
  }
  function validateSpecies() {
    const  species =  form.species.trim()
    const regex = /^[A-Za-z\s]+$/

    if(!species){
      speciesIsValid.value = false
      speciesErrorMessage.value = 'Species is required'
      return false
    }
    
    if ( species.length < 2) {
      speciesIsValid.value = false
      speciesErrorMessage.value = 'Species must be at least 3 characters long'
      return false
    }
    
    if (!regex.test( species)) {
      speciesIsValid.value = false
      speciesErrorMessage.value = 'Species must not start with a number and no special characters'
      return false
    }
    
    speciesIsValid.value = true
    speciesErrorMessage.value = ''
    return true
  }

  const breedIsValid = ref(null)
  const breedErrorMessage = ref('')

  const breedValidationClass = computed(() => {
    if (breedIsValid.value === true) return 'is-valid'
    if (breedIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateBreed() {
    const breed = form.breed.trim()
    const regex = /^[A-Za-z\s]+$/

    if (breed === '') {
      breedErrorMessage.value = 'Breed is required.'
      breedIsValid.value = false
      return false
    }

    if (breed.length < 3) {
      breedIsValid.value = false
      breedErrorMessage.value = 'Breed must be at least 3 characters long'
      return false
    }
    if(!regex.test(breed)){
      breedIsValid.value = false
      breedErrorMessage.value = 'Breed must not start with a number and no special characters'
      return false
    }
    breedIsValid.value = true
    breedErrorMessage.value = ''
    return true
  }

  const colorIsValid = ref(null)
  const colorErrorMessage = ref('')

  const colorValidationClass = computed(() => {
    if (colorIsValid.value === true) return 'is-valid'
    if (colorIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateColor() {
    const color = form.color.trim()
    const regex = /^[A-Za-z\s]+$/

    if (color === '') {
      colorIsValid.value = false
      colorErrorMessage.value = 'Color is required.'
      return false
    }

    if (color.length < 3) {
      colorIsValid.value = false
      colorErrorMessage.value = 'Color must be at least 3 characters long'
      return false
    }

    if(!regex.test(color)){
      colorIsValid.value = false
      colorErrorMessage.value = 'Color must not start with a number and no special characters'
      return false
    }

    colorErrorMessage.value = ''
    colorIsValid.value = true
    return true
  }

  const sexIsValid = ref(null)
  const sexErrorMessage = ref('')

  const sexValidationClass = computed(() => {
    if (sexIsValid.value === true) return 'is-valid'
    if (sexIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateSex() {
    const sex = form.sex

    sexIsValid.value = null
    sexErrorMessage.value = ''

    if (!sex) {
      sexIsValid.value = false
      sexErrorMessage.value = 'Sex is required'
      return false
    }

    sexIsValid.value = true
    sexErrorMessage.value = ''
    return true
  }

  const ageIsValid = ref(null)
  const ageErrorMessage = ref('')

  const ageValidationClass = computed(() => {
    if (ageIsValid.value === true) return 'is-valid'
    if (ageIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateAge() {
    const age = form.age_estimate.trim()
    const regex = /^\d+\s+(weeks?|months?|years?)\s+old$/i
    
    if (age === '') {
      ageIsValid.value = false
      ageErrorMessage.value = 'Age is required.'
      return false
    }

    if (!regex.test(age)) {
      ageIsValid.value = false
      ageErrorMessage.value = 'Age must be like "6 months old", "2 years old", or "10 weeks old"'
      return false
    }

    ageIsValid.value = true
    ageErrorMessage.value = ''
    return true
  }

  const sizeIsValid = ref(null)
  const sizeErrorMessage = ref('')

  const sizeValidationClass = computed(() => {
    if (sizeIsValid.value === true) return 'is-valid'
    if (sizeIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateSize(){
    const size = form.size
    if(!size){
      sizeIsValid.value = false
      sizeErrorMessage.value = 'Size is required.'
      return false
    }
    sizeIsValid.value = true
    sizeErrorMessage.value = ''
    return true
  }

  const distinctiveFeaturesIsValid = ref(null)
  const distinctiveFeaturesErrorMessage = ref('')

  const distinctiveFeaturesValidationClass = computed(() => {
    if (distinctiveFeaturesIsValid.value === true) return 'is-valid'
    if (distinctiveFeaturesIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateDistinctiveFeatures() {
    const distinctiveFeatures = form.distinctive_features.trim()
    const regex = /^(?!\d)[A-Za-z0-9\s'"-.!?]+$/

    if (distinctiveFeatures === '') {
      distinctiveFeaturesIsValid.value = true
      distinctiveFeaturesErrorMessage.value = 'This field can be empty.'
      return true
    }

    if (distinctiveFeatures.length < 3) {
      distinctiveFeaturesIsValid.value = false
      distinctiveFeaturesErrorMessage.value = 'Distinctive Features must be at least 3 characters long'
      return false
    }

    if(!regex.test(distinctiveFeatures)){
      distinctiveFeaturesIsValid.value = false
      distinctiveFeaturesErrorMessage.value = 'Distinctive Features must not start with numbers'
      return false
    }

    distinctiveFeaturesIsValid.value = true
    distinctiveFeaturesErrorMessage.value = ''
    return true
  }

  const conditionIsValid = ref(null)
  const conditionErrorMessage = ref('')

  const conditionValidationClass = computed(() => {
    if(conditionIsValid.value === true) return 'is-valid'
    if(colorIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateCondition(){
    const condition = form.condition.trim()
    const regex = /^[A-Za-z\s]+$/

    if(!condition){
      conditionIsValid.value = true
      conditionErrorMessage.value = "This field can be empty."
      return true
    }
    if(condition.length < 4){
      conditionIsValid.value = false
      conditionErrorMessage.value = "Condition is too short."
      return false
    }
    if(!regex.test(condition)){
      conditionIsValid.value = false
      conditionErrorMessage.value = "Condition must not start with a number and no special characters"
      return false
    }
    conditionIsValid.value = true
    conditionErrorMessage.value = ""
    return true
  }

  const shelterIsValid = ref(null)
  const shelterErrorMessage = ref('')
  const shelterValidationClass = computed(() => {
    if(shelterIsValid.value === true) return 'is-valid'
    if(shelterIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateShelter(){
    const shelter = form.temporary_shelter.trim()
    const regex = /^[A-Za-z\s]+$/

    if(!shelter){
      shelterIsValid.value = true
      shelterErrorMessage.value = "This field can be empty."
      return true
    }

    if(shelter.length < 4){
      shelterIsValid.value = false
      shelterErrorMessage.value = "Temporary Shelter is too short."
      return false
    }

    if(!regex.test(shelter)){
      shelterIsValid.value = false
      shelterErrorMessage.value = "Temporary Shelter must not start with a number and no special characters."
      return false
    }

    shelterIsValid.value = true
    shelterErrorMessage.value = ""
    return true
  }

  const locationIsValid = ref(null)
  const locationErrorMessage = ref('')

  const locationValidationClass = computed(() => {
    if(locationIsValid.value === true) return 'is-valid'
    if(locationIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateLocation(){
    const location = form.found_location.trim()
    const regex = /^[\p{L}0-9\s.,#'"\-()/]{5,100}$/u

    if (!location) {
      locationIsValid.value = false
      locationErrorMessage.value = "Found location is required."
      return false
    }

    if (!regex.test(location)) {
      locationIsValid.value= false
      locationErrorMessage.value  = "Enter a valid location (letters, numbers, spaces, and basic punctuation, min 5 characters)."
      return false
    }

    locationIsValid.value = true
    locationErrorMessage.value = ""
    return true
  }

  const dateIsValid = ref(null)
  const dateErrorMessage = ref('')

  const dateValidationClass = computed(() => {
    if(dateIsValid.value === true) return 'is-valid'
    if(dateIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateDate(){
    const date = form.found_date

    if(!date){
      dateIsValid.value = false
      dateErrorMessage.value = "Found date is required."
      return false
    }

    const selectedDate = new Date(date)
    const today = new Date()

    selectedDate.setHours(0, 0, 0, 0)
    today.setHours(0, 0, 0, 0)

    if(selectedDate > today){
      dateIsValid.value = false
      dateErrorMessage.value = "Found date can't in the future."
      return false
    }

    dateIsValid.value = true
    dateErrorMessage.value = ''
    return true

  }

  function submitForm(){
    const formData = new FormData()

    formData.append('_method', 'PUT')
    formData.append('species', form.species || '')
    formData.append('breed', form.breed || '')
    formData.append('color', form.color || '')
    formData.append('sex', form.sex|| '')
    formData.append('age_estimate', form.age_estimate || '')
    formData.append('size', form.size || '')
    formData.append('distinctive_features', form.distinctive_features || '')
    formData.append('found_location', form.found_location|| '')
    formData.append('found_date', form.found_date || '')
    formData.append('status', form.status || '')
    formData.append('type','found')
    formData.append('user_id',form.user_id)

    if (form.image instanceof File) {
      formData.append('image', form.image)
    }

    const isSpeciesValid = validateSpecies()
    const isBreedValid = validateBreed()
    const isColorValid = validateColor()
    const isSexValid = validateSex()
    const isAgeValid = validateAge()
    const isSizeValid = validateSize()
    const isDistinctiveFeaturesValid = validateDistinctiveFeatures()
    const isLocationValid = validateLocation()
    const isDateValid = validateDate()
    const imageInvalid = imageErrorMessage.value
    const isConditionValid = validateCondition()
    const isShelterValid = validateShelter()
    const isStatusValid = validateStatus()

    if(!isSpeciesValid || !isBreedValid || !isColorValid || !isSexValid || !isAgeValid || !isSizeValid || !isDistinctiveFeaturesValid || !isLocationValid || !isDateValid || imageInvalid || !isStatusValid|| !isConditionValid || !isShelterValid){
      return
    }
    form.post(`/reports/${reportId.value}`,{
      data: formData,
      forceFormData: true,
      preserveScroll: false,
      preserveState: false,
      onSuccess: () => {
        closeModal()
     },
    })

  }
  function closeModal(){
    const modalEl = document.getElementById('updateFoundReportModal')
    const modal = Modal.getInstance(modalEl)
    if (modal) {
      modal.hide()
    }

    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove())
    document.body.classList.remove('modal-open')
    document.body.style.removeProperty('overflow')
    document.body.style.removeProperty('padding-right')
    
    reportId.value = null
    reportAnimalSpecies.value = null
    reportAnimalBreed.value = null
    reportAnimalColor.value = null
    reportAnimalSex.value = null
    reportAnimalAgeEstimate.value = null
    reportAnimalSize.value = null
    reportAnimalDistinctiveFeatures.value = null
    reportAnimalFoundLocation.value = null
    reportAnimalFoundDate.value = null
    reportAnimalStatus.value = null
    reportAnimalCondition.value = null
    reportAnimalTemporaryShelter.value = null

    form.reset()

    speciesIsValid.value = null
    speciesErrorMessage.value = null

    breedIsValid.value = null
    breedErrorMessage.value = ''

    sexIsValid.value = null
    sexErrorMessage.value = ''
    
    ageIsValid.value = null
    ageErrorMessage.value = ''

    sizeIsValid.value = null
    sizeErrorMessage.value = ''

    colorIsValid.value = null
    colorErrorMessage.value = ''

    distinctiveFeaturesIsValid.value = null
    distinctiveFeaturesErrorMessage.value = ''

    conditionIsValid.value = null
    conditionErrorMessage.value = ''

    shelterIsValid.value = null 
    shelterErrorMessage.value = ''

    locationIsValid.value = null
    locationErrorMessage.value = ''

    dateIsValid.value = null
    dateErrorMessage.value = ''

    statusIsValid.value = null
    statusErrorMessage.value = ''

    imageErrorMessage.value = ''
    const fileInput = document.getElementById('found_image')
    if (fileInput) {
      fileInput.value = ''
      fileInput.classList.remove('is-valid', 'is-invalid')
    }
  }
  
</script>