<template>
  <div class="modal fade me-2" id="createStaffAccountModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="createStaffAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-sm-scrollable modal-lg">
      <div class="modal-content ">
        <div class="modal-header bg-info-subtle">
          <i class="bi bi-plus-circle-fill me-3 text-primary fs-2"></i>
          <h5 class="modal-title">Create a New Staff Account</h5>
        </div>
        <form @submit="submitForm" class="">
        <div class="modal-body bg-info-subtle border-0">
          <div class="row g-2 mt-2">
            <div class="col-12 col-md-6 form-floating">
              <input type="text" name="first_name" class="form-control" placeholder="First Name" aria-label="First Name" id="floating_first_name" autocomplete="true" autofocus :class="firstNameValidationClass" @blur="validateFirstName" v-model="firstNameValue">
              <label for="floating_first_name" class="form-label fw-bold">First Name</label>
              <small class="invalid-feedback fw-bold">{{ firstNameErrorMessage }}</small>
            </div>
            <div class="col-12 col-md-6 form-floating">
              <input type="text" name="last_name" class="form-control" placeholder="Last Name" aria-label="Last Name" id="floating_last_name" autocomplete="true" autofocus :class="lastNameValidationClass" @blur="validateLastName" v-model="lastNameValue">
              <label for="floating_last_name" class="form-label fw-bold">Last Name</label>
              <small class="invalid-feedback fw-bold">{{ lastNameErrorMessage }}</small>
            </div>
          </div>

          <div class="row g-2 mt-2">
            <div class="col-12 col-md-6 form-floating">
              <input type="email" name="email" class="form-control" placeholder="Email" aria-label="Email" id="floating_email" autocomplete="true" autofocus :class="emailValidationClass" @blur="validateEmail" v-model="emailValue">
              <label for="floating_email" class="form-label fw-bold">Email</label>
              <small class="invalid-feedback fw-bold">{{ emailErrorMessage }}</small>
            </div>
            <div class="col-12 col-md-6 form-floating">
              <input type="tel" id="floating_contact_number" :class="contactNumberValidationClass" @blur="validateContactNumber" name="contact_number" class="form-control" placeholder="Contact Number" aria-label="Contact Number" v-model="contactNumberValue">
              <label for="floating_contact_number" class="form-label">Contact Number</label>
              <small class="invalid-feedback fw-bold">{{ contactNumberErrorMessage }}</small>
            </div>
          </div>

          <div class="row g-2 mt-2">
            <div class="col-12 col-md-6 form-floating">
              <input type="text" name="password" class="form-control" placeholder="Password" aria-label="Password" id="floating_password" autocomplete="true" autofocus :class="passwordValidationClass" @blur="validatePassword" v-model="passwordValue">
              <label for="floating_password" class="form-label fw-bold">Password</label>
              <small class="invalid-feedback fw-bold">{{ passwordErrorMessage }}</small>
            </div>
            <div class="col-12 col-md-6 form-floating">
              <input type="text" name="password_confirmation" class="form-control" placeholder="Password Confirmation" aria-label="Password Confirmation" id="floating_password_confirmation" autocomplete="true" autofocus :class="passwordConfirmationValidationClass" @blur="validatePasswordConfirmation" v-model="passwordConfirmationValue">
              <label for="floating_password_confirmation" class="form-label fw-bold">Password Confirmation</label>
              <small class="invalid-feedback fw-bold">{{ passwordConfirmationErrorMessage }}</small>
            </div>
          </div>
          
        </div>
        <div class="modal-footer bg-info-subtle">
          <button class="btn btn-primary me-1" type="submit">Create Staff Account</button>
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

  const firstNameValue = ref('')
  const firstNameIsValid = ref(null) 
  const firstNameErrorMessage = ref('')

  const firstNameValidationClass = computed(() => {
    if (firstNameIsValid.value === true) return 'is-valid'
    if (firstNameIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateFirstName() {
    const first_name = firstNameValue.value.trim()
    const regex = /^[A-Za-z0-9\s'-]+$/

    if(!first_name){
      firstNameIsValid.value = false
      firstNameErrorMessage.value = 'First Name is required'
      return false
    }

    if (first_name.length < 2) {
      firstNameIsValid.value = false
      firstNameErrorMessage.value = 'First Name must be at least 2 characters long'
      return false
    }
    
    if (!regex.test(first_name)) {
      firstNameIsValid.value = false
      firstNameErrorMessage.value = 'First Name must not start with a number and no special characters'
      return false
    }
    
    firstNameIsValid.value = true
    firstNameErrorMessage.value = ''
    return true
  }

  const lastNameValue = ref('')
  const lastNameIsValid = ref(null) 
  const lastNameErrorMessage = ref('')

  const lastNameValidationClass = computed(() => {
    if (lastNameIsValid.value === true) return 'is-valid'
    if (lastNameIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateLastName() {
    const last_name = lastNameValue.value.trim()
    const regex = /^[A-Za-z0-9\s'-]+$/

    if(!last_name){
      lastNameIsValid.value = false
      lastNameErrorMessage.value = 'Last Name is required'
      return false
    }

    if (last_name.length < 1) {
      lastNameIsValid.value = false
      lastNameErrorMessage.value = 'Last Name must be at least 2 characters long'
      return false
    }
    
    if (!regex.test(last_name)) {
      lastNameIsValid.value = false
      lastNameErrorMessage.value = 'Last Name must not start with a number and no special characters'
      return false
    }
    
    lastNameIsValid.value = true
    lastNameErrorMessage.value = ''
    return true
  }

  const emailValue = ref('')
  const emailIsValid = ref(null)
  const emailErrorMessage = ref('')

  const emailValidationClass = computed(() => {
    if (emailIsValid.value === true) return 'is-valid'
    if (emailIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateEmail() {
    const email = emailValue.value.trim()
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/

    if (!email) {
      emailIsValid.value = false
      emailErrorMessage.value = 'Email is required'
      return false
    }

    if (!regex.test(email)) {
      emailIsValid.value = false
      emailErrorMessage.value = 'Please enter a valid email address'
      return false
    }

    emailIsValid.value = true
    emailErrorMessage.value = ''
    return true
  }

  const contactNumberValue = ref('')
  const contactNumberIsValid = ref(null)
  const contactNumberErrorMessage = ref('')

  const contactNumberValidationClass = computed(() => {
    if(contactNumberIsValid.value === true) return 'is-valid'
    if(contactNumberIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateContactNumber(){
    const contact_number = contactNumberValue.value.trim()
    const regex = /^09\d{9}$/

    if(!contact_number){
      contactNumberIsValid.value = false
      contactNumberErrorMessage.value = "Contact number is required."
      return false
    }

    if(!regex.test(contact_number)){
      contactNumberIsValid.value = false
      contactNumberErrorMessage.value = "Please enter a valid PH mobile number (e.g., 09171234567)."
      return false
    }
    contactNumberIsValid.value = true
    contactNumberErrorMessage.value = ""
    return true
  }

  const passwordValue = ref('')
  const passwordIsValid = ref(null)
  const passwordErrorMessage = ref('')

  const passwordValidationClass = computed(() => {
    if(passwordIsValid.value === true) return 'is-valid'
    if(passwordIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validatePassword(){
    const password = passwordValue.value.trim()

    if(!password){
      passwordIsValid.value = false
      passwordErrorMessage.value = "Password is required."
      return false
    }

    if(password.length < 8){
      passwordIsValid.value = false
      passwordErrorMessage.value = "Password must be at least 8 characters long."
      return false
    }

    passwordIsValid.value = true
    passwordErrorMessage.value = ""
    return true
  }

  const passwordConfirmationValue = ref('')
  const passwordConfirmationIsValid = ref(null)
  const passwordConfirmationErrorMessage = ref('')

  const passwordConfirmationValidationClass = computed(() => {
    if(passwordConfirmationIsValid.value === true) return 'is-valid'
    if(passwordConfirmationIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validatePasswordConfirmation(){
    const password_confirmation = passwordConfirmationValue.value.trim()

    if(!password_confirmation){
      passwordConfirmationIsValid.value = false
      passwordConfirmationErrorMessage.value = "Password confirmation is required."
      return false
    }

    if(password_confirmation !== passwordValue.value){
      passwordConfirmationIsValid.value = false
      passwordConfirmationErrorMessage.value = "Password confirmation does not match the password."
      return false
    }

    passwordConfirmationIsValid.value = true
    passwordConfirmationErrorMessage.value = ""
    return true
  }
  function submitForm(event) {
    event.preventDefault()
    const form = event.target
    const formData = new FormData(event.target)
    
    const isFirstNameValid = validateFirstName()
    const isLastNameValid = validateLastName()
    const isEmailValid = validateEmail()
    const isContactNumberValid = validateContactNumber()
    const isPasswordValid = validatePassword()
    const isPasswordConfirmationValid = validatePasswordConfirmation()

    if (!isFirstNameValid || !isLastNameValid || !isEmailValid || !isContactNumberValid || !isPasswordValid || !isPasswordConfirmationValid)  {
      return 
    }
    router.post('/users', formData, {
      forceFormData: true,
      preserveScroll: false,
      preserveState: false,
      onSuccess: () => {
        closeModal()
        form.reset()
      },
    })
  }

  function closeModal(){
    const modalEl = document.getElementById('createStaffAccountModal')
    const modal = Modal.getInstance(modalEl)
    if (modal) {
      modal.hide()
    }
    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove())
    document.body.classList.remove('modal-open')
    document.body.style.removeProperty('overflow')
    document.body.style.removeProperty('padding-right')

    firstNameValue.value = ''
    firstNameIsValid.value = null
    firstNameErrorMessage.value = ''

    
  }
</script>