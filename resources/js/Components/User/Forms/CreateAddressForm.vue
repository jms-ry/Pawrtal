<template>
  <form @submit="submitForm" method="POST">
    <div class="card bg-warning-subtle border-0 p-3 p-md-5">
      <input type="hidden" name="user_id" class="form-control" :value="user?.id">
      <div class="row g-2">
        <div class="col-12 col-md-6 form-floating">
          <input type="text" name="barangay" :class="barangayValidationClass" v-model="barangayValue" @blur="validateBarangay" class="form-control" placeholder="Barangay" aria-label="Barangay" id="floating_barangay">
          <label for="floating_barangay" class="form-label">Barangay</label>
          <small class="invalid-feedback fw-bold">{{ barangayErrorMessage }}</small>
        </div>
        <div class="col-12 col-md-6 form-floating">
          <input type="text" name="municipality" :class="municipalityValidationClass" v-model="municipalityValue" @blur="validateMunicipality" class="form-control" placeholder="Municipality" aria-label="Municipality" id="floating_municipality">
          <label for="floating_municipality" class="form-label">Municipality</label>
          <small class="invalid-feedback fw-bold">{{ municipalityErrorMessage }}</small>
        </div>
      </div>
      <div class="row g-2 mt-2">
        <div class="col-12 col-md-6 form-floating">
          <input type="text" id="floating_province" name="province" :class="provinceValidationClass" v-model="provinceValue" @blur="validateProvince" class="form-control" placeholder="Province" aria-label="Province">
          <label for="floating_province" class="form-label">Province</label>
          <small class="invalid-feedback fw-bold">{{ provinceErrorMessage }}</small>
        </div>
        <div class="col-12 col-md-6 form-floating">
          <input type="text" id="floating_zip_code" name="zip_code" :class="zipCodeValidationClass" v-model="zipCodeValue" @blur="validateZipCode" class="form-control" placeholder="Zip Code" aria-label="Zip Code">
          <label for="floating_zip_code" class="form-label">Zip Code</label>
          <small class="invalid-feedback fw-bold">{{ zipCodeErrorMessage }}</small>
        </div>
      </div>
    </div>
    <div class="card-footer border-0 bg-warning-subtle">
      <div class="justify-content-end d-none d-md-flex mt-3 mt-md-0">
        <button type="submit" class="btn btn-success fw-bolder">Create Address</button>
      </div>
      <div class="d-md-none">
        <button type="submit" class="btn btn-success w-100 fw-bolder">Create Address</button>
      </div>
    </div>
  </form>
</template>

<script setup>
  import { router} from '@inertiajs/vue3'
  import { ref, computed } from 'vue'

  const props = defineProps({
    user: {
      type: Object,
      default: () => null
    },
  })

  const barangayValue = ref('')
  const barangayIsValid = ref(null) 
  const barangayErrorMessage = ref('')

  const barangayValidationClass = computed(() => {
    if (barangayIsValid.value === true) return 'is-valid'
    if (barangayIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateBarangay(){
    const barangay = barangayValue.value.trim()
    const regex = /^(?!\d)[A-Za-z0-9\s'-]+$/

    if(!barangay){
      barangayIsValid.value = false
      barangayErrorMessage.value = "Barangay is required"
      return false
    }

    if(barangay.length < 5){
      barangayIsValid.value = false
      barangayErrorMessage.value = "Barangay must at least 5 characters."
      return false
    }

    if(!regex.test(barangay)){
      barangayIsValid.value = false
      barangayErrorMessage.value = "Barangay must not start with a number."
      return false
    }

    barangayIsValid.value = true
    barangayErrorMessage.value = ""
    return true
  }

  const municipalityValue = ref('')
  const municipalityIsValid = ref(null) 
  const municipalityErrorMessage = ref('')

  const municipalityValidationClass = computed(() => {
    if (municipalityIsValid.value === true) return 'is-valid'
    if (municipalityIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateMunicipality(){
    const municipality = municipalityValue.value.trim()
    const regex = /^(?!\d)[A-Za-z0-9\s'-]+$/

    if(!municipality){
      municipalityIsValid.value = false
      municipalityErrorMessage.value = "Municipality is required"
      return false
    }

    if(municipality.length < 5){
      municipalityIsValid.value = false
      municipalityErrorMessage.value = "Municipality must at least 5 characters."
      return false
    }

    if(!regex.test(municipality)){
      municipalityIsValid.value = false
      municipalityErrorMessage.value = "Municipality must not start with a number."
      return false
    }

    municipalityIsValid.value = true
    municipalityErrorMessage.value = ""
    return true
  }

  const provinceValue = ref('')
  const provinceIsValid = ref(null) 
  const provinceErrorMessage = ref('')

  const provinceValidationClass = computed(() => {
    if (provinceIsValid.value === true) return 'is-valid'
    if (provinceIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateProvince(){
    const province = provinceValue.value.trim()
    const regex = /^(?!\d)[A-Za-z0-9\s'-]+$/

    if(!province){
      provinceIsValid.value = false
      provinceErrorMessage.value = "Province is required"
      return false
    }

    if(province.length < 5){
      provinceIsValid.value = false
      provinceErrorMessage.value = "Province must at least 5 characters."
      return false
    }

    if(!regex.test(province)){
      provinceIsValid.value = false
      provinceErrorMessage.value = "Province must not start with a number."
      return false
    }

    provinceIsValid.value = true
    provinceErrorMessage.value = ""
    return true
  }

  const zipCodeValue = ref('')
  const zipCodeIsValid = ref(null) 
  const zipCodeErrorMessage = ref('')

  const zipCodeValidationClass = computed(() => {
    if (zipCodeIsValid.value === true) return 'is-valid'
    if (zipCodeIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateZipCode(){
    const zipCode = zipCodeValue.value.trim()
    const regex = /^\d{4}$/

    if(!zipCode){
      zipCodeIsValid.value = false
      zipCodeErrorMessage.value = "ZipCode is required"
      return false
    }

    if(zipCode.length > 4){
      zipCodeIsValid.value = false
      zipCodeErrorMessage.value = "ZipCode must be 4 numeric characters only."
      return false
    }

    if(!regex.test(zipCode)){
      zipCodeIsValid.value = false
      zipCodeErrorMessage.value = "Zip Code must be numeric."
      return false
    }

    zipCodeIsValid.value = true
    zipCodeErrorMessage.value = ""
    return true
  }
  function submitForm(event) {
    event.preventDefault()
    const formData = new FormData(event.target)
    
    const isBarangayValid = validateBarangay()
    const isMunicipalityValid = validateMunicipality()
    const isProvinceValid = validateProvince()
    const isZipCodeValid = validateZipCode()

    if(!isBarangayValid || !isMunicipalityValid || !isProvinceValid ||!isZipCodeValid){
      return
    }

    router.post('/addresses',formData,{
      preserveScroll: false,
      preserveState: false,
    })
  }
</script>