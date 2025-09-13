<template>
  <div class="modal fade me-2" id="createRescueProfileModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="createRescueProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-sm-scrollable modal-lg">
      <div class="modal-content ">
        <div class="modal-header bg-info-subtle">
          <i class="bi bi-plus-circle-fill me-3 text-primary fs-2"></i>
          <h5 class="modal-title">Create a New Rescue Profile</h5>
        </div>
        <form @submit="submitForm" class="">
        <div class="modal-body bg-info-subtle border-0">
          <div class="row g-2 mt-2">
            <div class="col-12 col-md-4 form-floating">
              <input type="text" name="name" class="form-control" placeholder="Rescue name" aria-label="Rescue name" id="floating_rescue_name" autocomplete="true" autofocus :class="nameValidationClass" @blur="validateName" v-model="nameValue">
              <label for="floating_rescue_name" class="form-label fw-bold">Name</label>
              <small class="invalid-feedback fw-bold">{{ nameErrorMessage }}</small>
            </div>
            <div class="col-12 col-md-4 form-floating">
              <input type="text" name="species" id="floating_rescue_species" class="form-control" placeholder="Rescue name" aria-label="Rescue name" autocomplete="true" autofocus :class="speciesValidationClass" @blur="validateSpecies" v-model="speciesValue" >
              <label for="floating_rescue_species" class="form-label fw-bold">Species</label>
              <small class="invalid-feedback fw-bold">{{ speciesErrorMessage }}</small>
            </div>
            <div class="col-12 col-md-4 form-floating">
              <input type="text" name="breed" class="form-control" placeholder="Rescue breed" aria-label="Rescue breed" id="floating_rescue_breed" :class="breedValidationClass" @blur="validateBreed" v-model="breedValue">
              <label for="floating_rescue_breed" class="form-label fw-bold">Breed</label>
              <small class="invalid-feedback fw-bold">{{ breedErrorMessage }}</small>
              <small class="valid-feedback text-dark fw-light">{{ breedErrorMessage }}</small>
            </div>
          </div>

          <div class="row g-2 mt-2">
            <div class="col-12 col-md-4 form-floating">
              <select name="sex" :class="sexValidationClass" v-model="sexValue" @blur="validateSex"  id="floating_sex" class="form-select" aria-label="sex-select"  >
                <option selected hidden value="">Sex</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
              </select>
              <label for="floating_sex" class="form-label fw-bold">Select a sex</label>
              <small class="invalid-feedback fw-bold">{{ sexErrorMessage }}</small>
            </div>
            <div class="col-12 col-md-4 form-floating">
              <input type="text" name="age" :class="ageValidationClass" @blur="validateAge" v-model="ageValue" class="form-control" placeholder="Rescue age" aria-label="Rescue age" id="floating_rescue_age">
              <label for="floating_rescue_age" class="form-label fw-bold">Age (e.g 6 months old)</label>
              <small class="invalid-feedback fw-bold">{{ ageErrorMessage }}</small>
               <small class="valid-feedback text-dark fw-light">{{ ageErrorMessage }}</small>
            </div>
            <div class="col-12 col-md-4 form-floating">
              <select name="size" :class="sizeValidationClass" @blur="validateSize" v-model="sizeValue" id="floating_rescue_size" class="form-select" aria-label="size-select"  >
                <option selected hidden value="">Size</option>
                <option value="small">Small</option>
                <option value="medium">Medium</option>
                <option value="large">Large</option>
              </select>
              <label for="floating_rescue_size" class="form-label fw-bold">Select size</label>
              <small class="valid-feedback text-dark fw-light">{{ sizeErrorMessage }}</small>
            </div>
          </div>

          <div class="row g-2 mt-2">
            <div class="col-12 col-md-4 form-floating">
              <input type="text" name="color" :class="colorValidationClass" @blur="validateColor" v-model="colorValue" class="form-control" placeholder="Rescue color" aria-label="Rescue color" id="floating_rescue_color">
              <label for="floating_rescue_color" class="form-label fw-bold">Color</label>
              <small class="invalid-feedback fw-bold">{{ colorErrorMessage }}</small>
              <small class="valid-feedback text-dark fw-light">{{ colorErrorMessage }}</small>
            </div>
            <div class="col-12 col-md-4 form-floating">
              <input type="text" name="distinctive_features" :class="distinctiveFeaturesValidationClass" @blur="validateDistinctiveFeatures" v-model="distinctiveFeaturesValue" class="form-control" placeholder="Rescue distinctive features" aria-label="Rescue distinctive features" id="floating_rescue_distinctive_features">
              <label for="floating_rescue_distinctive_features" class="form-label fw-bold">Distinctive Features</label>
              <small class="invalid-feedback fw-bold">{{ distinctiveFeaturesErrorMessage }}</small>
              <small class="valid-feedback text-dark fw-light">{{ distinctiveFeaturesErrorMessage }}</small>
            </div>
            <div class="col-12 col-md-4 form-floating">
              <select name="health_status" :class="healthStatusValidationClass" v-model="healthStatusValue" @blur="validateHealthStatus" id="floating_health_status" class="form-select" aria-label="health-status-select"  >
                <option selected hidden value="">Health Status</option>
                <option value="healthy">Healthy</option>
                <option value="sick">Sick</option>
                <option value="injured">Injured</option>
              </select>
              <label for="floating_health_status" class="form-label fw-bold">Select health status</label>
              <small class="invalid-feedback fw-bold">{{ healthStatusErrorMessage }}</small>
            </div>
          </div>

          <div class="row g-2 mt-2">
            <div class="col-12 col-md-4 form-floating">
              <select name="vaccination_status" :class="vaccinationStatusValidationClass" v-model="vaccinationStatusValue" @blur="validateVaccinationStatus" id="floating_vaccination_status" class="form-select" aria-label="vaccination-status-select"  >
                <option selected hidden value="">Vaccination Status</option>
                <option value="vaccinated">Vaccinated</option>
                <option value="partially_vaccinated">Partially Vaccinated</option>
                <option value="not_vaccinated">Not Vaccinated</option>
              </select>
              <label for="floating_vaccination_status" class="form-label fw-bold">Select vaccination status</label>
              <small class="invalid-feedback fw-bold">{{ vaccinationStatusErrorMessage }}</small>
            </div>
            <div class="col-12 col-md-4 form-floating">
              <select name="spayed_neutered" :class="spayedNeuteredValidationClass" v-model="spayedNeuteredValue" @blur="validateSpayedNeutered" id="floating_spayed_neutered" class="form-select" aria-label="spayed-neutered-select"  >
                <option selected hidden value="">Spay/Neutered</option>
                <option value="true">Yes</option>
                <option value="false">No</option>
              </select>
              <label for="floating_spayed_neutered" class="form-label fw-bold">Is it spayed/neutered?</label>
              <small class="invalid-feedback fw-bold">{{ spayedNeuteredErrorMessage }}</small>
            </div>
            <div class="col-12 col-md-4 form-floating">
              <select name="adoption_status" :class="adoptionStatusValidationClass" v-model="adoptionStatusValue" @blur="validateAdoptionStatus" id="floating_adoption_status" class="form-select" aria-label="adoption-status-select"  >
                <option selected hidden value="">Adoption Status</option>
                <option value="available">Available</option>
                <option value="unavailable">Unavailable</option>
                <option value="adopted">Adopted</option>
              </select>
              <label for="floating_adoption_status" class="form-label fw-bold">Select adoption status</label>
              <small class="invalid-feedback fw-bold">{{ adoptionStatusErrorMessage }}</small>
            </div>
          </div>

          <div class="row g-2 mt-2">
            <div class="col-12 col-md-6">
              <label for="profile_image" class="form-label fw-bold">Upload Profile Image</label>
              <input type="file" name="profile_image" id="profile_image" class="form-control" accept="image/*" @change="validateProfileImage" >
              <small class="invalid-feedback fw-bold">{{ profileImageError }}</small>
            </div>
            <div class="col-12 col-md-6">
              <label for="images" class="form-label fw-bold">Additional Image/s</label>
              <input type="file" name="images[]" id="images" class="form-control" accept="image/*" multiple @change="validateImages">
              <small class="invalid-feedback fw-bold">{{ imagesError }}</small>
              <small class="valid-feedback text-dark fw-light">{{ imagesError }}</small>
            </div>
          </div>

          <div class="row g-2 mt-2">
            <div class="col-12 form-floating">
              <textarea name="description" :class="descriptionValidationClass" @blur="validateDescription" v-model="descriptionValue" id="floating_rescue_description" class="form-control" placeholder="Rescue description" aria-label="Rescue description" style="height: 100px"  ></textarea>
              <label for="floating_rescue_description" class="form-label fw-bold">Description</label>
              <small class="invalid-feedback fw-bold">{{ descriptionErrorMessage }}</small>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-info-subtle">
          <button class="btn btn-primary me-1" type="submit">Create Rescue Profile</button>
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
  import { ref, computed } from 'vue'

  const nameValue = ref('')
  const nameIsValid = ref(null) 
  const nameErrorMessage = ref('')

  const nameValidationClass = computed(() => {
    if (nameIsValid.value === true) return 'is-valid'
    if (nameIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateName() {
    const name = nameValue.value.trim()
    
    if(!name){
      nameIsValid.value = false
      nameErrorMessage.value = 'Name is required'
      return false
    }

    if (name.length < 2) {
      nameIsValid.value = false
      nameErrorMessage.value = 'Name must be at least 2 characters long'
      return false
    }
    
    if (/^\d/.test(name)) {
      nameIsValid.value = false
      nameErrorMessage.value = 'Name must not start with a number'
      return false
    }
    
    nameIsValid.value = true
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
    if(!species){
      speciesIsValid.value = false
      speciesErrorMessage.value = 'Species is required'
      return false
    }
    
    if ( species.length < 2) {
       speciesIsValid.value = false
       speciesErrorMessage.value = 'Species must be at least 2 characters long'
      return false
    }
    
    if (/^\d/.test( species)) {
       speciesIsValid.value = false
       speciesErrorMessage.value = 'Species must not start with a number'
      return false
    }
    
     speciesIsValid.value = true
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

    breedIsValid.value = null
    breedErrorMessage.value = 'This field can be empty.'

    if (breed === '') {
      breedIsValid.value = true
      return true
    }

    if (breed.length < 3) {
      breedIsValid.value = false
      breedErrorMessage.value = 'Breed must be at least 3 characters long'
      return false
    }

    breedIsValid.value = true
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

    ageIsValid.value = null
    ageErrorMessage.value = 'This field can be empty.'

    if (age === '') {
      ageIsValid.value = true
    } else {
      const regex = /^\d+\s+(weeks?|months?|years?)\s+old$/i
      if (regex.test(age)) {
        ageIsValid.value = true
      } else {
        ageIsValid.value = false
        ageErrorMessage.value = 'Age must be like "6 months old", "2 years old", or "10 weeks old"'
      }
    }

    return ageIsValid.value
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
    sizeErrorMessage.value = 'This field can be empty.'
    if(!size){
      sizeIsValid.value = true
      return true
    }
    sizeIsValid.value = true
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

    colorIsValid.value = null
    colorErrorMessage.value = 'This field can be empty.'

    if (color === '') {
      colorIsValid.value = true
      return true
    }

    if (color.length < 3) {
      colorIsValid.value = false
      colorErrorMessage.value = 'Color must be at least 3 characters long'
      return false
    }

    colorIsValid.value = true
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

    distinctiveFeaturesIsValid.value = null
    distinctiveFeaturesErrorMessage.value = 'This field can be empty.'

    if (distinctiveFeatures === '') {
      distinctiveFeaturesIsValid.value = true
      return true
    }

    if (distinctiveFeatures.length < 3) {
      distinctiveFeaturesIsValid.value = false
      distinctiveFeaturesErrorMessage.value = 'Distinctive Features must be at least 3 characters long'
      return false
    }

    distinctiveFeaturesIsValid.value = true
    return true
  }

  const healthStatusValue = ref('')
  const healthStatusIsValid = ref(null)
  const healthStatusErrorMessage = ref('')

  const healthStatusValidationClass = computed(() => {
    if (healthStatusIsValid.value === true) return 'is-valid'
    if (healthStatusIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateHealthStatus() {
    const healthStatus = healthStatusValue.value

    healthStatusIsValid.value = null
    healthStatusErrorMessage.value = ''

    if (!healthStatus) {
      healthStatusIsValid.value = false
      healthStatusErrorMessage.value = 'Health Status is required'
      return false
    }

    healthStatusIsValid.value = true
    return true
  }

  const vaccinationStatusValue = ref('')
  const vaccinationStatusIsValid = ref(null)
  const vaccinationStatusErrorMessage = ref('')

  const vaccinationStatusValidationClass = computed(() => {
    if (vaccinationStatusIsValid.value === true) return 'is-valid'
    if (vaccinationStatusIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateVaccinationStatus() {
    const vaccinationStatus = vaccinationStatusValue.value

    vaccinationStatusIsValid.value = null
    vaccinationStatusErrorMessage.value = ''

    if (!vaccinationStatus) {
      vaccinationStatusIsValid.value = false
      vaccinationStatusErrorMessage.value = 'Vaccination Status is required'
      return false
    }

    vaccinationStatusIsValid.value = true
    return true
  }

  const spayedNeuteredValue = ref('')
  const spayedNeuteredIsValid = ref(null)
  const spayedNeuteredErrorMessage = ref('')

  const spayedNeuteredValidationClass = computed(() => {
    if (spayedNeuteredIsValid.value === true) return 'is-valid'
    if (spayedNeuteredIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateSpayedNeutered() {
    const spayedNeutered = spayedNeuteredValue.value

    spayedNeuteredIsValid.value = null
    spayedNeuteredErrorMessage.value = ''

    if (!spayedNeutered) {
      spayedNeuteredIsValid.value = false
      spayedNeuteredErrorMessage.value = 'Spayed/Neutered is required'
      return false
    }

    spayedNeuteredIsValid.value = true
    return true
  }

  const adoptionStatusValue = ref('')
  const adoptionStatusIsValid = ref(null)
  const adoptionStatusErrorMessage = ref('')

  const adoptionStatusValidationClass = computed(() => {
    if (adoptionStatusIsValid.value === true) return 'is-valid'
    if (adoptionStatusIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateAdoptionStatus() {
    const adoptionStatus = adoptionStatusValue.value

    adoptionStatusIsValid.value = null
    adoptionStatusErrorMessage.value = ''

    if (!adoptionStatus) {
      adoptionStatusIsValid.value = false
      adoptionStatusErrorMessage.value = 'Adoption Status is required'
      return false
    }

    adoptionStatusIsValid.value = true
    return true
  }

  const descriptionValue = ref('')
  const descriptionIsValid = ref(null) 
  const descriptionErrorMessage = ref('')

  const descriptionValidationClass = computed(() => {
    if (descriptionIsValid.value === true) return 'is-valid'
    if (descriptionIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateDescription() {
    const description = descriptionValue.value.trim()
    
    if(!description){
      descriptionIsValid.value = false
      descriptionErrorMessage.value = 'Description is required'
      return false
    }

    if (description.length < 2) {
      descriptionIsValid.value = false
      descriptionErrorMessage.value = 'Description must be at least 2 characters long'
      return false
    }
    
    if (/^\d/.test(description)) {
      descriptionIsValid.value = false
      descriptionErrorMessage.value = 'Description must not start with a number'
      return false
    }
    
    descriptionIsValid.value = true
    return true
  }

  const profileImageError = ref("")

  function validateProfileImage(event = null) {
    const input = event ? event.target : document.getElementById("profile_image")
    const file = input.files[0]

    if (!file) {
      profileImageError.value = "Profile image is required."
      input.classList.add('is-invalid')
      return false
    }

    const allowedTypes = ["image/jpeg", "image/png", "image/jpg"]
    if (!allowedTypes.includes(file.type)) {
      profileImageError.value = "Only JPEG, JPG, or PNG files are allowed."
      input.classList.add('is-invalid')
      return false
    }

    if (file.size > 2 * 1024 * 1024) {
      profileImageError.value = "File must not exceed 2MB."
      input.classList.add('is-invalid')
      return false
    }
    
    profileImageError.value = ""
    input.classList.remove('is-invalid')
    input.classList.add('is-valid')
    return true
  }

  const imagesError = ref("")

  function validateImages(event = null) {
    const input = event ? event.target : document.getElementById("images")
    const files = input.files

    if (!files.length) {
      imagesError.value = "This field is optional."
      input.classList.remove("is-invalid")
      input.classList.add("is-valid")
      return true
    }

    const allowedTypes = ["image/jpeg", "image/png", "image/jpg"]

    for (let i = 0; i < files.length; i++) {
      const file = files[i]

      if (!allowedTypes.includes(file.type)) {
        imagesError.value = `File "${file.name}" is not a valid format. Only JPEG, JPG, PNG allowed.`
        input.classList.add("is-invalid")
        input.classList.remove("is-valid")
        return false
      }

      if (file.size > 2 * 1024 * 1024) {
        imagesError.value = `File "${file.name}" exceeds 2MB size limit.`
        input.classList.add("is-invalid")
        input.classList.remove("is-valid")
        return false
      }
    }

    imagesError.value = ""
    input.classList.remove("is-invalid")
    input.classList.add("is-valid")
    return true
  }

  function submitForm(event) {
    event.preventDefault()
    const form = event.target
    const formData = new FormData(event.target)
    
    const isNameValid = validateName()
    const isSpeciesValid = validateSpecies()
    const isBreedValid = validateBreed()
    const isSexValid = validateSex()
    const isAgeValid = validateAge()
    const isSizeValid = validateSize()
    const isColorValid = validateColor()
    const isDistinctiveFeaturesValid = validateDistinctiveFeatures()
    const isHealthStatusValid = validateHealthStatus()
    const isVaccinationStatusValid = validateVaccinationStatus()
    const isAdoptionStatusValid = validateAdoptionStatus()
    const isSpayedNeuteredValid = validateSpayedNeutered()
    const isDescriptionValid = validateDescription()
    const isProfileImageValid = validateProfileImage()
    const isImagesValid = validateImages()

    if (!isNameValid 
        || !isSpeciesValid 
        || !isBreedValid 
        || !isSexValid 
        || !isAgeValid 
        || !isSizeValid
        || !isColorValid
        || !isDistinctiveFeaturesValid
        || !isHealthStatusValid
        || !isVaccinationStatusValid
        || !isAdoptionStatusValid
        || !isSpayedNeuteredValid
        || !isDescriptionValid
        || !isProfileImageValid
        || !isImagesValid

      ) {
      return 
    }
    router.post('/rescues', formData, {
      forceFormData: true,
      preserveScroll: true,
      preserveState: false,
      onSuccess: () => {
        closeModal()
        form.reset()
      },
    })
  }

  function closeModal(){
    const modalEl = document.getElementById('createRescueProfileModal')
    const modal = Modal.getInstance(modalEl)
    if (modal) {
      modal.hide()
    }
    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove())
    document.body.classList.remove('modal-open')
    document.body.style.removeProperty('overflow')
    document.body.style.removeProperty('padding-right')

    nameValue.value = ''
    nameIsValid.value = null
    nameErrorMessage.value = ''

    speciesValue.value = ''
    speciesIsValid.value = null
    speciesErrorMessage.value = ''

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

    healthStatusValue.value = ''
    healthStatusIsValid.value = null
    healthStatusErrorMessage.value = ''

    vaccinationStatusValue.value = ''
    vaccinationStatusIsValid.value = null
    vaccinationStatusErrorMessage.value = ''

    spayedNeuteredValue.value = ''
    spayedNeuteredIsValid.value = null
    spayedNeuteredErrorMessage.value = ''

    adoptionStatusValue.value = ''
    adoptionStatusIsValid.value = null
    adoptionStatusErrorMessage.value = ''

    descriptionValue.value = ''
    descriptionIsValid.value = null
    descriptionErrorMessage.value = ''
    
    profileImageError.value = ''
    const fileInput = document.getElementById('profile_image')
    if (fileInput) {
      fileInput.value = ''
      fileInput.classList.remove('is-valid', 'is-invalid')
    }

    imagesError.value = ''
    const imageInput = document.getElementById('images')
    if (fileInput) {
      imageInput.value = ''
      imageInput.classList.remove('is-valid', 'is-invalid')
    }
  }
</script>