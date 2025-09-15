<template>
  <form @submit.prevent="submitForm" method="POST">
    <div class="card bg-warning-subtle border-0 p-3 p-md-5">
      <input type="hidden" name="user_id" class="form-control" :value="user.id">
      <div class="row g-2">
        <div class="col-12 col-md-6 form-floating">
          <input type="text" name="house_structure" :class="houseStructureValidationClass" @blur="validateHouseStructure" v-model="houseStructureValue" class="form-control" placeholder="House Structure" aria-label="House Structure" id="floating_house_structure" >
          <label for="floating_house_structure" class="form-label">House Structure (e.g Apartment, Tiny House, Cabin, etc.)</label>
          <small class="invalid-feedback fw-bold">{{ houseStructureErrorMessage }}</small>
        </div>
        <div class="col-12 col-md-6 form-floating">
          <input type="number" min="1" name="household_members" :class="householdMembersValidationClass" @blur="validateHouseholdMembers" v-model="householdMembersValue" class="form-control" placeholder="House Members" aria-label="House Members" id="floating_house_members">
          <label for="floating_house_members" class="form-label">House members (including yourself)</label>
          <small class="invalid-feedback fw-bold">{{ householdMembersErrorMessage }}</small>
        </div>
      </div>
      <div class="row g-2 mt-2">
        <div class="col-12 col-md-6 form-floating">
          <select name="have_children" :class="haveChildrenValidationClass" @blur="validateHaveChildren" v-model="haveChildrenValue" id="floating_have_children" class="form-select" aria-label="children-select">
            <option hidden value="" >Are there children in the house?</option>
            <option value="true" >Yes</option>
            <option value="false" >No</option>
          </select>
          <label for="floating_have_children" class="form-label">Are there children in the house?</label>
          <small class="invalid-feedback fw-bold">{{ haveChildrenErrorMessage }}</small>
        </div>
        <div class="col-12 col-md-6 form-floating" v-show="haveChildrenValue === 'true'" >
          <input type="number" min="1" name="number_of_children" :class="numberOfChildrenValidationClass" @blur="validateNumberOfChildren" v-model="numberOfChildrenValue" class="form-control" placeholder="Number of Children" aria-label="Number of Children" id="floating_number_of_children">
          <label for="floating_number_of_children" class="form-label">Number of Children</label>
          <small class="invalid-feedback fw-bold" id="numberOfChildrenFeedback">{{ numberOfChildrenErrorMessage }}</small>
        </div>
      </div>
      <div class="row g-2 mt-2">
        <div class="col-12 col-md-4 form-floating">
          <select name="has_other_pets" id="floating_has_other_pets" :class="hasOtherPetsValidationClass" @blur="validateHasOtherPets" v-model="hasOtherPetsValue" class="form-select" aria-label="other-pet-select">
            <option hidden value="">Do you have other pets?</option>
            <option value="true" >Yes</option>
            <option value="false" >No</option>
          </select>
          <label for="floating_has_other_pets" class="form-label">Do you have other pets?</label>
          <small class="invalid-feedback fw-bold">{{ hasOtherPetsErrorMessage }}</small>
        </div>
        <div class="col-12 col-md-4 form-floating" v-show="hasOtherPetsValue === 'true'">
          <input type="text" id="floating_current_pets" name="current_pets" :class="currentPetsValidationClass" @blur="validateCurrentPets" v-model="currentPetsValue" class="form-control" placeholder="Current Pet/s" aria-label="Current Pet/s">
          <label for="floating_current_pets" class="form-label">Current Pet/s (Separate with comma(,) if more than one) </label>
          <small class="invalid-feedback fw-bold">{{ currentPetsErrorMessage }}</small>
        </div>
        <div class="col-12 col-md-4 form-floating" v-show="hasOtherPetsValue === 'true'">
          <input type="number" min="1" id="floating_number_of_current_pets" name="number_of_current_pets" :class="numberOfCurrentPetsValidationClass" @blur="validateNumberOfCurrentPets" v-model="numberOfCurrentPetsValue" class="form-control" placeholder="Number of Current Pet/s" aria-label="Number of Current Pet/s">
          <label for="floating_number_of_current_pets" class="form-label">Number of Current Pet/s</label>
          <small class="invalid-feedback fw-bold">{{ numberOfCurrentPetsErrorMessage }}</small>
        </div>
      </div>
    </div>
    <div class="card-footer border-0 bg-warning-subtle">
      <div class="justify-content-end d-none d-md-flex mt-3 mt-md-0">
        <button type="submit" class="btn btn-success fw-bolder">Create Household</button>
      </div>
      <div class="d-md-none">
        <button type="submit" class="btn btn-success w-100 fw-bolder">Create Household</button>
      </div>
    </div>
  </form>
</template>

<script setup>
  import { router} from '@inertiajs/vue3'
  import { ref, computed, watch} from 'vue'
  const props = defineProps({
    user: {
      type: Object,
      default: () => null
    },
  })

  const houseStructureValue = ref('')
  const houseStructureIsValid = ref(null)
  const houseStructureErrorMessage = ref('')

  const houseStructureValidationClass = computed(() => {
    if(houseStructureIsValid.value === true) return 'is-valid'
    if(houseStructureIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateHouseStructure(){
    const house_structure = houseStructureValue.value.trim()
    const regex = /^(?!\d)[A-Za-z\s\-()/&]+$/

    if(!house_structure){
      houseStructureIsValid.value = false
      houseStructureErrorMessage.value = "House Structure is required."
      return false
    }

    if(house_structure.length < 3){
      houseStructureIsValid.value = false
      houseStructureErrorMessage.value = "House Structure must be atleast 3 characters long."
      return false
    }

    if(!regex.test(house_structure)){
      houseStructureIsValid.value = false
      houseStructureErrorMessage.value = "House Structure must no start with or contain a number."
      return false
    }

    houseStructureIsValid.value = true
    houseStructureErrorMessage.value = ""
    return true
  }

  const householdMembersValue = ref('')
  const householdMembersIsValid = ref(null)
  const householdMembersErrorMessage = ref('')

  const householdMembersValidationClass = computed(() => {
    if(householdMembersIsValid.value === true) return 'is-valid'
    if(householdMembersIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateHouseholdMembers(){
    const household_members = householdMembersValue.value

    if(!household_members){
      householdMembersIsValid.value = false
      householdMembersErrorMessage.value = "Household members is required."
      return false
    }else{
      householdMembersIsValid.value = true
      householdMembersErrorMessage.value = ''
      return true
    }
  }

  const haveChildrenValue = ref('')
  const haveChilrenIsValid = ref(null)
  const haveChildrenErrorMessage = ref('')

  const haveChildrenValidationClass = computed(() => {
    if(haveChilrenIsValid.value === true) return 'is-valid'
    if(haveChilrenIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateHaveChildren(){
    const have_children = haveChildrenValue.value

    if(!have_children){
      haveChilrenIsValid.value = false
      haveChildrenErrorMessage.value = "Have children is required."
      return false
    }else{
      haveChilrenIsValid.value = true
      haveChildrenErrorMessage.value = ''
      return true
    }
  }
  const numberOfChildrenValue = ref('')
  const numberOfChildrenIsValid = ref(null)
  const numberOfChildrenErrorMessage = ref('')

  const numberOfChildrenValidationClass = computed(() => {
    if(numberOfChildrenIsValid.value === true) return 'is-valid'
    if(numberOfChildrenIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateNumberOfChildren(){
    const field = document.getElementById("number_of_children_div")

    if (!field || field.offsetParent === null) {
      numberOfChildrenIsValid.value = true
      numberOfChildrenErrorMessage.value = ''
      return true
    }
    const number = numberOfChildrenValue.value

    if(!number){
      numberOfChildrenIsValid.value = false
      numberOfChildrenErrorMessage.value = "Number of Children is required."
      return false
    }

    numberOfChildrenIsValid.value = true
    numberOfChildrenErrorMessage.value = ''
    return true
  }

  const hasOtherPetsValue = ref('')
  const hasOtherPetsIsValid = ref(null)
  const hasOtherPetsErrorMessage = ref('')

  const hasOtherPetsValidationClass = computed(() => {
    if(hasOtherPetsIsValid.value === true) return 'is-valid'
    if(hasOtherPetsIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateHasOtherPets(){
    const has_other_pets = hasOtherPetsValue.value

    if(!has_other_pets){
      hasOtherPetsIsValid.value = false
      hasOtherPetsErrorMessage.value = "Has other pets is required."
      return false
    }else{
      hasOtherPetsIsValid.value = true
      hasOtherPetsErrorMessage.value = ''
      return true
    }
  }

  const currentPetsValue = ref('')
  const currentPetsIsValid = ref(null)
  const currentPetsErrorMessage = ref('')

  const currentPetsValidationClass = computed(() => {
    if(currentPetsIsValid.value === true) return 'is-valid'
    if(currentPetsIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateCurrentPets(){
    const field = document.getElementById('current_pets_div')

    if (!field || field.offsetParent === null) {
      currentPetsIsValid.value = true
      currentPetsErrorMessage.value = ''
      return true
    }
    const current_pets = currentPetsValue.value.trim()
    const regex = /^\s*(?:\d+\s+)?[A-Za-z]+(?:\s+[A-Za-z]+)*(?:\s*(?:,|&|and)\s*(?:\d+\s+)?[A-Za-z]+(?:\s+[A-Za-z]+)*)*\s*$/
    if(!current_pets){
      currentPetsIsValid.value = false
      currentPetsErrorMessage.value = "Current pets is required."
      return false
    }
    if(!regex.test(current_pets)){
      currentPetsIsValid.value = false
      currentPetsErrorMessage.value = "Please list your pets separated by commas, 'and', or '&' (e.g., '2 dogs, 3 cats & a fish')."
      return false
    }
    currentPetsIsValid.value = true
    currentPetsErrorMessage.value = ''
    return true
  }

  const numberOfCurrentPetsValue = ref('')
  const numberOfCurrentPetsIsValid = ref(null)
  const numberOfCurrentPetsErrorMessage = ref('')

  const numberOfCurrentPetsValidationClass = computed(() => {
    if(numberOfCurrentPetsIsValid.value === true) return 'is-valid'
    if(numberOfCurrentPetsIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateNumberOfCurrentPets(){
    const field = document.getElementById('number_of_current_pets_div')

    if (!field || field.offsetParent === null) {
      numberOfCurrentPetsIsValid.value = true
      numberOfCurrentPetsErrorMessage.value = ''
      return true
    }

    const number_of_pets = numberOfCurrentPetsValue.value

    if(!number_of_pets){
      numberOfCurrentPetsIsValid.value = false
      numberOfCurrentPetsErrorMessage.value = "Number of current pets is required."
      return false
    }

    numberOfCurrentPetsIsValid.value = true
    numberOfCurrentPetsErrorMessage.value = ''
    return true
  }
  function submitForm(event) {
    event.preventDefault()
    const formData = new FormData(event.target)

    const isHouseStructureValid = validateHouseStructure()
    const isHouseholdMembersValid = validateHouseholdMembers()
    const isHaveChildrenValid = validateHaveChildren()
    const isNumberOfChildrenValid = validateNumberOfChildren()
    const isHasOtherPetsValid = validateHasOtherPets()
    const isCurrentPetsValid = validateCurrentPets()
    const isNumberOfCurrentPetsValid = validateNumberOfCurrentPets()

    if(!isHouseStructureValid 
      || !isHouseholdMembersValid 
      || !isHaveChildrenValid 
      || !isNumberOfChildrenValid 
      || !isHasOtherPetsValid
      || !isCurrentPetsValid 
      || !isNumberOfCurrentPetsValid){
      return
    }

    router.post('/households',formData,{
      preserveScroll: false,
      preserveState: false,
    })
  }
</script>