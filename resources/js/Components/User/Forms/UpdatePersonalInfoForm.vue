<template>
  <form @submit.prevent="submitForm" class="">
    <div class="card bg-warning-subtle border-0 p-3 p-md-5">
      <div class="row g-2">
        <div class="col-12 col-md-6 form-floating">
          <input type="text" name="first_name" :class="firstNameValidationClass" @blur="validateFirstName" class="form-control" placeholder="First name" aria-label="First name" id="floating_first_name" v-model="form.first_name">
          <label for="floating_first_name" class="form-label">First Name</label>
          <small class="invalid-feedback fw-bold">{{ firstNameErrorMessage }}</small>
        </div>
        <div class="col-12 col-md-6 form-floating">
          <input type="text" name="last_name" :class="lastNameValidationClass" @blur="validateLastName" class="form-control" placeholder="Last name" aria-label="Last name" id="floating_last_name" v-model="form.last_name">
          <label for="floating_last_name" class="form-label">Last Name</label>
          <small class="invalid-feedback fw-bold">{{ lastNameErrorMessage }}</small>
        </div>
      </div>
      <div class="row g-2 mt-2">
        <div class="col-12 col-md-6 form-floating">
          <input type="tel" id="floating_contact_number" :class="contactNumberValidationClass" @blur="validateContactNumber" name="contact_number" class="form-control" placeholder="Contact Number" aria-label="Contact Number" v-model="form.contact_number">
          <label for="floating_contact_number" class="form-label">Contact Number</label>
          <small class="invalid-feedback fw-bold">{{ contactNumberErrorMessage }}</small>
        </div>
        <div class="col-12 col-md-6 form-floating">
          <input type="email" id="floating_email" :class="emailValidationClass" @blur="validateEmail" name="email" class="form-control" placeholder="Email" aria-label="Email" v-model="form.email">
          <label for="floating_email" class="form-label">Email</label>
          <small class="invalid-feedback fw-bold">{{ emailErrorMessage }}</small>
        </div>
      </div>
    </div>
    <div class="card-footer border-0 bg-warning-subtle">
      <div class="justify-content-end d-none d-md-flex mt-3 mt-md-0">
        <button type="submit" class="btn btn-info fw-bolder">Update Information</button>
      </div>
      <div class="d-md-none">
        <button type="submit" class="btn btn-info w-100 fw-bolder">Update Information</button>
      </div>
    </div>
  </form>
</template>

<script setup>
  import { useForm } from '@inertiajs/vue3';
  import {ref, computed } from 'vue'

  const props = defineProps({
    user: {
      type: Object,
      default: () => null
    }
  })

  const form = useForm({
    first_name: props.user.first_name,
    last_name: props.user.last_name,
    contact_number: props.user.contact_number,
    email: props.user.email
  })

  const firstNameIsValid = ref(null)
  const firstNameErrorMessage = ref('')

  const firstNameValidationClass = computed(() =>{
    if(firstNameIsValid.value === true ) return 'is-valid'
    if(firstNameIsValid.value === false ) return 'is-invalid'
    return ''
  })

  function validateFirstName(){
    const first_name = form.first_name.trim()

    if(!first_name){
      firstNameIsValid.value = false
      firstNameErrorMessage.value = "First Name is required."
      return false
    }

    if(first_name.length < 3 ){
      firstNameIsValid.value = false
      firstNameErrorMessage.value = "First Name must at least 3 characters long"

      if(/^\d/.test(first_name)){
        firstNameIsValid.value = false
        firstNameErrorMessage.value = "First Name must not start with a number."
        return false
      }
      return false
    }else{
      if(/\d/.test(first_name)){
        firstNameIsValid.value = false
        firstNameErrorMessage.value = "First Name must not contain a number."
        return false
      }
    }

    firstNameIsValid.value = true
    firstNameErrorMessage.value = ""
    return true
  }

  const lastNameIsValid = ref(null)
  const lastNameErrorMessage = ref('')

  const lastNameValidationClass = computed(() =>{
    if(lastNameIsValid.value === true ) return 'is-valid'
    if(lastNameIsValid.value === false ) return 'is-invalid'
    return ''
  })

  function validateLastName(){
    const last_name = form.last_name.trim()

    if(!last_name){
      lastNameIsValid.value = false
      lastNameErrorMessage.value = "Last Name is required."
      return false
    }

    if(last_name.length < 3 ){
      lastNameIsValid.value = false
      lastNameErrorMessage.value = "Last Name must at least 3 characters long"

      if(/^\d/.test(last_name)){
        lastNameIsValid.value = false
        lastNameErrorMessage.value = "Last Name must not start with a number."
        return false
      }
      return false
    }else{
      if(/\d/.test(last_name)){
        lastNameIsValid.value = false
        lastNameErrorMessage.value = "Last Name must not contain a number."
        return false
      }
    }
    lastNameIsValid.value = true
    lastNameErrorMessage.value = ""
    return true

  }

  const contactNumberIsValid = ref(null)
  const contactNumberErrorMessage = ref('')

  const contactNumberValidationClass = computed(() => {
    if(contactNumberIsValid.value === true) return 'is-valid'
    if(contactNumberIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateContactNumber(){
    const contact_number = form.contact_number.trim()
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

  const emailIsValid = ref(null)
  const emailErrorMessage = ref('')

  const emailValidationClass = computed(() => {
    if(emailIsValid.value === true) return 'is-valid'
    if(emailIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateEmail(){
    const email = form.email.trim()
    const regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/

    if(!email){
      emailIsValid.value = false
      emailErrorMessage.value = "Email is required."
      return false
    }
    if(!regex.test(email)){
      emailIsValid.value = false
      emailErrorMessage.value = "Please input a valid email address."
      return false
    }

    emailIsValid.value = true
    emailErrorMessage.value = ''
    return true
  }
  function submitForm(){

    const isFirstNameValid = validateFirstName()
    const isLastNameValid = validateLastName()
    const isContactNumberValid = validateContactNumber()
    const isEmailValid = validateEmail()

    if(!isFirstNameValid || !isLastNameValid || !isContactNumberValid || !isEmailValid){
      return
    }
    form.put(`/users/${props.user.id}`,{
      preserveScroll: true,
      preserveState: false,
    })
  }
  
</script>