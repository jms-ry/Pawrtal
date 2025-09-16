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
              <input type="text" name="species" class="form-control" placeholder="Animal species" aria-label="Animal species" id="floating_animal_species" autocomplete="true" :class="speciesValidationClass" @blur="validateSpecies" v-model="speciesValue">
              <label for="floating_animal_species" class="form-label fw-bold">Species (e.g Dog, Cat, etc.)</label>
              <small class="invalid-feedback fw-bold">{{ speciesErrorMessage }}</small>
            </div>
            <div class="col-12 col-md-4 form-floating">
              <input type="text" name="breed" class="form-control" placeholder="Animal breed" aria-label="Animal breed" id="floating_animal_breed" autocomplete="true" :class="breedValidationClass" @blur="validateBreed" v-model="breedValue">
              <label for="floating_animal_breed" class="form-label fw-bold">Breed</label>
              <small class="invalid-feedback fw-bold">{{ breedErrorMessage }}</small>
            </div>
            <div class="col-12 col-md-4 form-floating">
              <input type="text" name="color" class="form-control" placeholder="Animal color" aria-label="Animal color" id="floating_animal_color" autocomplete="true" :class="colorValidationClass" @blur="validateColor" v-model="colorValue">
              <label for="floating_animal_color" class="form-label fw-bold">Color</label>
              <small class="invalid-feedback fw-bold">{{ colorErrorMessage }}</small>
            </div>
          </div>

          <div class="row g-2 mt-2">
            <div class="col-12 col-md-4 form-floating">
              <select name="sex" id="floating_sex" class="form-select" aria-label="sex-select" :class="sexValidationClass" @blur="validateSex" v-model="sexValue">
                <option selected hidden value="">Sex</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="unknown">Unknown</option>
              </select>
              <label for="floating_sex" class="form-label fw-bold">Select a sex</label>
              <small class="invalid-feedback fw-bold">{{ sexErrorMessage }}</small>
            </div>
            <div class="col-12 col-md-4 form-floating">
              <input type="text" name="age_estimate" class="form-control" placeholder="Animal age estimate" aria-label="Animal age estimate" id="floating_animal_age_estimate" autocomplete="true" :class="ageValidationClass" @blur="validateAge" v-model="ageValue">
              <label for="floating_animal_age_estimate" class="form-label fw-bold">Age Estimate (e.g 6 months old)</label>
              <small class="invalid-feedback fw-bold">{{ ageErrorMessage }}</small>
            </div>
            <div class="col-12 col-md-4 form-floating">
              <select name="size" id="floating_animal_size" class="form-select" aria-label="size-select" :class="sizeValidationClass" @blur="validateSize" v-model="sizeValue">
                <option selected hidden value="">Size</option>
                <option value="small">Small</option>
                <option value="medium">Medium</option>
                <option value="large">Large</option>
              </select>
              <label for="floating_animal_size" class="form-label fw-bold">Select size</label>
              <small class="invalid-feedback fw-bold">{{ sizeErrorMessage }}</small>
            </div>
          </div>

          <div class="row g-2 mt-2">
            <div class="col-12 col-md-4 form-floating">
              <input type="text" name="distinctive_features" class="form-control" placeholder="Animal distinctive features" aria-label="Animal distinctive features" id="floating_animal_distinctive_features" :class="distinctiveFeaturesValidationClass" @blur="validateDistinctiveFeatures" v-model="distinctiveFeaturesValue">
              <label for="floating_animal_distinctive_features" class="form-label fw-bold">Distinctive Features</label>
              <small class="invalid-feedback fw-bold">{{ distinctiveFeaturesErrorMessage }}</small>
              <small class="valid-feedback text-dark fw-light">{{ distinctiveFeaturesErrorMessage }}</small>
            </div>
            <div class="col-12 col-md-4 form-floating">
              <input type="text" name="condition" class="form-control" placeholder="Animal condition" aria-label="Animal condition" id="floating_animal_condition" :class="conditionValidationClass" @blur="validateCondition" v-model="conditionValue">
              <label for="floating_animal_distinctive_features" class="form-label fw-bold">Condition</label>
              <small class="invalid-feedback fw-bold">{{ conditionErrorMessage }}</small>
              <small class="valid-feedback text-dark">{{ conditionErrorMessage }}</small>
            </div>
            <div class="col-12 col-md-4 form-floating">
              <input type="text" name="temporary_shelter" class="form-control" placeholder="Animal temporary" aria-label="Animal temporary shelter" id="floating_animal_temporary_shelter" :class="shelterValidationClass" @blur="validateShelter" v-model="shelterValue">
              <label for="floating_animal_temporary_shelter" class="form-label fw-bold">Temporary Shelter</label>
              <small class="invalid-feedback fw-bold">{{ shelterErrorMessage }}</small>
              <small class="valid-feedback text-dark">{{ shelterErrorMessage }}</small>
            </div>
          </div>

          <div class="row g-2 mt-2">
            <div class="col-12 col-md-8 form-floating">
              <input type="text" name="found_location" class="form-control" placeholder="Animal found location" aria-label="Animal found location" id="floating_animal_found_location" :class="locationValidationClass" @blur="validateLocation" v-model="locationValue">
              <label for="floating_animal_found_location" class="form-label fw-bold">Found Location</label>
              <small class="invalid-feedback fw-bold">{{ locationErrorMessage }}</small>
            </div>
            <div class="col-12 col-md-4 form-floating">
              <input type="date" name="found_date" class="form-control" placeholder="Animal last seen date" aria-label="Animal found date" id="floating_animal_found_date" :class="dateValidationClass" @blur="validateDate" v-model="dateValue">
              <label for="floating_animal_found_date" class="form-label fw-bold">Found Date</label>
              <small class="invalid-feedback fw-bold">{{ dateErrorMessage }}</small>
            </div>
          </div>

          <div class="row g-2 mt-2">
            <div class="col-12">
              <label for="image" class="form-label fw-bold">Upload an Image</label>
              <input type="file" name="image" id="found_image" class="form-control" accept="image/*" @change="validateImage">
              <small class="invalid-feedback fw-bold">{{ imageErrorMessage }}</small>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-info-subtle">
          <button class="btn btn-primary me-1" type="submit">Submit Report</button>
          <button class="btn btn-danger" type="button" @click="closeModal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>
</template>

<script setup>
  import { router } from '@inertiajs/vue3'
  import { Modal } from 'bootstrap'
  import {ref,computed} from 'vue'

  const props = defineProps({
    user: {
      type: Object,
      default: () => null
    }
  })

  const  speciesValue = ref('')
  const  speciesIsValid = ref(null) 
  const  speciesErrorMessage = ref('')

  const speciesValidationClass = computed(() => {
    if (speciesIsValid.value === true) return 'is-valid'
    if (speciesIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateSpecies() {
    const  species =  speciesValue.value.trim()
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

  const breedValue = ref('')
  const breedIsValid = ref(null)
  const breedErrorMessage = ref('')

  const breedValidationClass = computed(() => {
    if (breedIsValid.value === true) return 'is-valid'
    if (breedIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateBreed() {
    const breed = breedValue.value.trim()
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

  const colorValue = ref('')
  const colorIsValid = ref(null)
  const colorErrorMessage = ref('')

  const colorValidationClass = computed(() => {
    if (colorIsValid.value === true) return 'is-valid'
    if (colorIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateColor() {
    const color = colorValue.value.trim()
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

  const sexValue = ref('')
  const sexIsValid = ref(null)
  const sexErrorMessage = ref('')

  const sexValidationClass = computed(() => {
    if (sexIsValid.value === true) return 'is-valid'
    if (sexIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateSex() {
    const sex = sexValue.value

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

  const ageValue = ref('')
  const ageIsValid = ref(null)
  const ageErrorMessage = ref('')

  const ageValidationClass = computed(() => {
    if (ageIsValid.value === true) return 'is-valid'
    if (ageIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateAge() {
    const age = ageValue.value.trim()
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

  const sizeValue = ref('')
  const sizeIsValid = ref(null)
  const sizeErrorMessage = ref('')

  const sizeValidationClass = computed(() => {
    if (sizeIsValid.value === true) return 'is-valid'
    if (sizeIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateSize(){
    const size = sizeValue.value
    if(!size){
      sizeIsValid.value = false
      sizeErrorMessage.value = 'Size is required.'
      return false
    }
    sizeIsValid.value = true
    sizeErrorMessage.value = ''
    return true
  }

  const distinctiveFeaturesValue = ref('')
  const distinctiveFeaturesIsValid = ref(null)
  const distinctiveFeaturesErrorMessage = ref('')

  const distinctiveFeaturesValidationClass = computed(() => {
    if (distinctiveFeaturesIsValid.value === true) return 'is-valid'
    if (distinctiveFeaturesIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateDistinctiveFeatures() {
    const distinctiveFeatures = distinctiveFeaturesValue.value.trim()
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
  const conditionValue = ref('')
  const conditionIsValid = ref(null)
  const conditionErrorMessage = ref('')

  const conditionValidationClass = computed(() => {
    if(conditionIsValid.value === true) return 'is-valid'
    if(colorIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateCondition(){
    const condition = conditionValue.value.trim()
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

  const shelterValue = ref('')
  const shelterIsValid = ref(null)
  const shelterErrorMessage = ref('')
  const shelterValidationClass = computed(() => {
    if(shelterIsValid.value === true) return 'is-valid'
    if(shelterIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateShelter(){
    const shelter = shelterValue.value.trim()
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

  const locationValue = ref('')
  const locationIsValid = ref(null)
  const locationErrorMessage = ref('')

  const locationValidationClass = computed(() => {
    if(locationIsValid.value === true) return 'is-valid'
    if(locationIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateLocation(){
    const location = locationValue.value.trim()
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

  const dateValue = ref('')
  const dateIsValid = ref(null)
  const dateErrorMessage = ref('')

  const dateValidationClass = computed(() => {
    if(dateIsValid.value === true) return 'is-valid'
    if(dateIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateDate(){
    const date = dateValue.value

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

  const imageErrorMessage = ref('')

  function validateImage(event = null){
    const input = event ? event.target : document.getElementById("found_image")
    const file = input.files[0]

    if (!file) {
      imageErrorMessage.value = "Image is required."
      input.classList.add('is-invalid')
      return false
    }

    const allowedExtensions = ['jpg', 'jpeg', 'png',]
    const fileExtension = file.name.split('.').pop().toLowerCase()
    if (!allowedExtensions.includes(fileExtension)) {
      input.classList.add('is-invalid')
      imageErrorMessage.value = "Only JPG, JPEG, PNG are allowed."
      return false
    }

    if (file.size > 2 * 1024 * 1024) {
      imageErrorMessage.value = "File must not exceed 2MB."
      input.classList.add('is-invalid')
      return false
    }
    
    imageErrorMessage.value = ""
    input.classList.remove('is-invalid')
    input.classList.add('is-valid')
    return true
  }
  function submitForm(event) {
    event.preventDefault()
    const form = event.target
    const formData = new FormData(event.target)

    const isSpeciesValid = validateSpecies()
    const isBreedValid = validateBreed()
    const isColorValid = validateColor()
    const isSexValid = validateSex()
    const isAgeValid = validateAge()
    const isSizeValid = validateSize()
    const isDistinctiveFeaturesValid = validateDistinctiveFeatures()
    const isLocationValid = validateLocation()
    const isDateValid = validateDate()
    const isImagesValid = validateImage()
    const isConditionValid = validateCondition()
    const isShelterValid = validateShelter()

    if(!isSpeciesValid || !isBreedValid || !isColorValid || !isSexValid || !isAgeValid || !isSizeValid || !isDistinctiveFeaturesValid || !isLocationValid || !isDateValid || !isImagesValid || !isConditionValid || !isShelterValid){
      return
    }
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

    speciesIsValid.value = null
    speciesValue.value = null
    speciesErrorMessage.value = null

    breedValue.value = ''
    breedIsValid.value = null
    breedErrorMessage.value = ''

    sexValue.value = ''
    sexIsValid.value = null
    sexErrorMessage.value = ''
        
    ageValue.value = ''
    ageIsValid.value = null
    ageErrorMessage.value = ''

    sizeValue.value = ''
    sizeIsValid.value = null
    sizeErrorMessage.value = ''

    colorValue.value = ''
    colorIsValid.value = null
    colorErrorMessage.value = ''

    distinctiveFeaturesValue.value = ''
    distinctiveFeaturesIsValid.value = null
    distinctiveFeaturesErrorMessage.value = ''

    conditionValue.value = ''
    conditionIsValid.value = null
    conditionErrorMessage.value = ''

    shelterValue.value = ''
    shelterIsValid.value = null 
    shelterErrorMessage.value = ''

    locationValue.value = ''
    locationIsValid.value = null
    locationErrorMessage.value = ''

    dateValue.value = ''
    dateIsValid.value = null
    dateErrorMessage.value = ''
    
    imageErrorMessage.value = ''
    const fileInput = document.getElementById('found_image')
    if (fileInput) {
      fileInput.value = ''
      fileInput.classList.remove('is-valid', 'is-invalid')
    }
  }
</script>
