<template>
  <div class="modal fade" id="createLostAnimalReportModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="createLostAnimalReportModalLabel">
    <div class="modal-dialog modal-dialog-centered modal-dialog-sm-scrollable modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-info-subtle">
          <i class="bi bi-plus-circle-fill me-3 text-primary fs-2"></i>
          <h5 class="modal-title">Create a New Lost Animal Report!</h5>
        </div>
        <form @submit="submitForm">
          <div class="modal-body bg-info-subtle border-0">
            <input type="hidden" name="type" class="form-control" value="lost">
            <input type="hidden" name="user_id" class="form-control" :value="user?.id">
            <div class="row g-2 mt-2">
              <div class="col-12 col-md-4 form-floating">
                <input type="text" name="animal_name" class="form-control" placeholder="Animal name" aria-label="Animal name" id="floating_animal_name" autocomplete="true" :class="animalNameValidationClass" @blur="validateAnimalName" v-model="animalNameValue">
                <label for="floating_animal_name" class="form-label fw-bold">Animal's Name</label>
                <small class="invalid-feedback fw-bold">{{ animalNameErrorMessage }}</small>
              </div>
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
            </div>

            <div class="row g-2 mt-2">
              <div class="col-12 col-md-4 form-floating">
                <input type="text" name="color" class="form-control" placeholder="Animal color" aria-label="Animal color" id="floating_animal_color" autocomplete="true" :class="colorValidationClass" @blur="validateColor" v-model="colorValue">
                <label for="floating_animal_color" class="form-label fw-bold">Color</label>
                <small class="invalid-feedback fw-bold">{{ colorErrorMessage }}</small>
              </div>
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
            </div>

            <div class="row g-2 mt-2">
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
              <div class="col-12 col-md-8 form-floating">
                <input type="text" name="distinctive_features" class="form-control" placeholder="Animal distinctive features" aria-label="Animal distinctive features" id="floating_animal_distinctive_features" :class="distinctiveFeaturesValidationClass" @blur="validateDistinctiveFeatures" v-model="distinctiveFeaturesValue">
                <label for="floating_animal_distinctive_features" class="form-label fw-bold">Distinctive Features</label>
                <small class="invalid-feedback fw-bold">{{ distinctiveFeaturesErrorMessage }}</small>
                <small class="valid-feedback text-dark fw-light">{{ distinctiveFeaturesErrorMessage }}</small>
              </div>
            </div>

            <div class="row g-2 mt-2">
              <div class="col-12 col-md-8 form-floating">
                <input type="text" name="last_seen_location" class="form-control" placeholder="Animal last seen location" aria-label="Animal last seen location" id="floating_animal_last_seen_location" :class="locationValidationClass" @blur="validateLocation" v-model="locationValue">
                <label for="floating_animal_last_seen_location" class="form-label fw-bold">Last Seen Location</label>
                <small class="invalid-feedback fw-bold">{{ locationErrorMessage }}</small>
              </div>
              <div class="col-12 col-md-4 form-floating">
                <input type="date" name="last_seen_date" class="form-control" placeholder="Animal last seen date" aria-label="Animal last seen date" id="floating_animal_last_seen_date" :class="dateValidationClass" @blur="validateDate" v-model="dateValue">
                <label for="floating_animal_last_seen_date" class="form-label fw-bold">Last Seen Date</label>
                <small class="invalid-feedback fw-bold">{{ dateErrorMessage }}</small>
              </div>
            </div>

            <div class="row g-2 mt-2">
              <div class="col-12">
                <label for="image" class="form-label fw-bold">Upload an Image</label>
                <input type="file" name="image" id="image" class="form-control" accept="image/*" @change="validateImage">
                <div class="form-text">Please upload a clear photo of the lost animal.</div>
                <small class="invalid-feedback fw-bold">{{ imageErrorMessage }}</small>
              </div>
            </div>
          </div>
          <div class="modal-footer bg-info-subtle">
            <button class="btn btn-primary me-1" type="submit">Submit Report</button>
            <button class="btn btn-danger" type="button"  @click="closeModal">Close</button>
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

  //Validate Animal Name
  const animalNameValue = ref('')
  const animalNameIsValid = ref(null)
  const animalNameErrorMessage = ref('')

  const animalNameValidationClass = computed(() => {
    if(animalNameIsValid.value === true) return 'is-valid'
    if(animalNameIsValid.value === false) return 'is-invalid'
    return ''
  })
  
  function validateAnimalName(){
    const name = animalNameValue.value.trim()
    const regex = /^[A-Za-z0-9\s'-]+$/

    if(!name){
      animalNameIsValid.value = false
      animalNameErrorMessage.value = "Animal Name is required."
      return false
    }

    if (name.length < 2) {
      animalNameIsValid.value = false
      animalNameErrorMessage.value = 'Animal Name must be at least 2 characters long'
      return false
    }
    
    if (!regex.test(name)) {
      animalNameIsValid.value = false
      animalNameErrorMessage.value = 'Animal Name must not start with a number and no special characters'
      return false
    }

    animalNameIsValid.value = true
    animalNameErrorMessage.value = ''
    return true
  }

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
    const regex = /^(?!\d)[A-Za-z0-9\s'-]+$/

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
      locationErrorMessage.value = "Last seen location is required."
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
      dateErrorMessage.value = "Last seen date is required."
      return false
    }

    const selectedDate = new Date(date)
    const today = new Date()
    selectedDate.setHours(0, 0, 0, 0)
    today.setHours(0, 0, 0, 0)

    if(selectedDate > today){
      dateIsValid.value = false
      dateErrorMessage.value = "Last seen date can't in the future."
      return false
    }

    dateIsValid.value = true
    dateErrorMessage.value = ''
    return true

  }

  const imageErrorMessage = ref('')

  function validateImage(event = null){
    const input = event ? event.target : document.getElementById("image")
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

    const isAnimalNameValid = validateAnimalName()
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

    if(!isAnimalNameValid || !isSpeciesValid || !isBreedValid || !isColorValid || !isSexValid || !isAgeValid || !isSizeValid || !isDistinctiveFeaturesValid || !isLocationValid || !isDateValid || !isImagesValid){
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
    const modalEl = document.getElementById('createLostAnimalReportModal')
    const modal = Modal.getInstance(modalEl)
    if (modal) {
      modal.hide()
    }

    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove())
    document.body.classList.remove('modal-open')
    document.body.style.removeProperty('overflow')
    document.body.style.removeProperty('padding-right')

    animalNameIsValid.value = null
    animalNameValue.value = ''
    animalNameErrorMessage.value = ''

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

    locationValue.value = ''
    locationIsValid.value = null
    locationErrorMessage.value = ''

    dateValue.value = ''
    dateIsValid.value = null
    dateErrorMessage.value = ''
    
    imageErrorMessage.value = ''
    const fileInput = document.getElementById('image')
    if (fileInput) {
      fileInput.value = ''
      fileInput.classList.remove('is-valid', 'is-invalid')
    }
  }
</script>
