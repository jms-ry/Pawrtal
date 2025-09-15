<template>
  <form @submit.prevent="submitForm" class="">
    <div class="card bg-warning-subtle border-0 p-3 p-md-5">
      <input type="hidden" name="user_id" v-model="form.user_id">
      <div class="row g-2">
        <div class="col-12 col-md-6 form-floating">
          <input type="text" name="house_structure" :class="houseStructureValidationClass" @blur="validateHouseStructure" class="form-control" placeholder="House Structure" aria-label="House Structure" id="floating_house_structure" v-model="form.house_structure">
          <label for="floating_house_structure" class="form-label">House Structure (e.g Apartment, Tiny House, Cabin, etc.)</label>
          <small class="invalid-feedback fw-bold">{{ houseStructureErrorMessage }}</small>
        </div>
        <div class="col-12 col-md-6 form-floating">
          <input type="number" min="1" name="household_members" :class="householdMembersValidationClass" @blur="validateHouseholdMembers" class="form-control" placeholder="House Members" aria-label="House Members" id="floating_house_members" v-model="form.household_members">
          <label for="floating_house_members" class="form-label">House members (including yourself)</label>
          <small class="invalid-feedback fw-bold">{{ householdMembersErrorMessage }}</small>
        </div>
      </div>
      <div class="row g-2 mt-2">
        <div class="col-12 col-md-6 form-floating">
          <select v-model="form.have_children" name="have_children" :class="haveChildrenValidationClass" @blur="validateHaveChildren" id="floating_have_children" class="form-select" aria-label="children-select" data-action="change->household#toggleNumberOfChildrenField" required>
            <option hidden >Are there children in the house?</option>
            <option value="true">Yes</option>
            <option value="false">No</option>
          </select>
          <label for="floating_have_children" class="form-label">Are there children in the house?</label>
          <small class="invalid-feedback fw-bold">{{ haveChildrenErrorMessage }}</small>
        </div>
        <div class="col-12 col-md-6 form-floating" v-show="form.have_children === 'true'" id="number_of_children_div">
          <input type="number" min="1" name="number_of_children" :class="numberOfChildrenValidationClass" @blur="validateNumberOfChildren" class="form-control" placeholder="Number of Children" aria-label="Number of Children" id="floating_number_of_children"  v-model="form.number_of_children">
          <label for="floating_number_of_children" class="form-label">Number of Children</label>
          <small class="invalid-feedback fw-bold" id="numberOfChildrenFeedback">{{ numberOfChildrenErrorMessage }}</small>
        </div>
      </div>
      <div class="row g-2 mt-2">
        <div class="col-12 col-md-4 form-floating">
          <select v-model="form.has_other_pets" name="has_other_pets" :class="hasOtherPetsValidationClass" @blur="validateHasOtherPets" id="floating_has_other_pets" class="form-select" aria-label="other-pet-select">
            <option hidden >Do you have other pets?</option>
            <option value="true">Yes</option>
            <option value="false">No</option>
          </select>
          <label for="floating_has_other_pets" class="form-label">Do you have other pets?</label>
          <small class="invalid-feedback fw-bold">{{ hasOtherPetsErrorMessage }}</small>
        </div>
        <div class="col-12 col-md-4 form-floating" v-show="form.has_other_pets === 'true'" id="current_pets_div">
          <input type="text" id="floating_current_pets" name="current_pets" :class="currentPetsValidationClass" @blur="validateCurrentPets" class="form-control" placeholder="Current Pet/s" aria-label="Current Pet/s"  v-model="form.current_pets">
          <label for="floating_current_pets" class="form-label">Current Pet/s (Separate with , if more than one) </label>
          <small class="invalid-feedback fw-bold">{{ currentPetsErrorMessage }}</small>
        </div>
        <div class="col-12 col-md-4 form-floating" v-show="form.has_other_pets === 'true'" id="number_of_current_pets_div">
          <input type="number" min="1" id="floating_number_of_current_pets" name="number_of_current_pets" :class="numberOfCurrentPetsValidationClass" @blur="validateNumberOfCurrentPets" class="form-control" placeholder="Number of Current Pet/s" aria-label="Number of Current Pet/s"  v-model="form.number_of_current_pets">
          <label for="floating_number_of_current_pets" class="form-label">Number of Current Pet/s</label>
          <small class="invalid-feedback fw-bold">{{ numberOfCurrentPetsErrorMessage }}</small>
        </div>
      </div>
    </div>
    <div class="card-footer border-0 bg-warning-subtle">
      <div class="justify-content-end d-none d-md-flex mt-3 mt-md-0">
        <button type="submit" class="btn btn-info fw-bolder me-2">Update Household</button>
        <button type="button" data-bs-toggle="modal" data-bs-target="#deleteHouseholdModal" :data-household-id="user.household.id" class="btn btn-danger fw-bolder">Delete Household</button>
      </div>
      <div class="d-md-none">
        <button type="submit" class="btn btn-info w-100 fw-bolder">Update Household</button>
        <button type="button" data-bs-toggle="modal" data-bs-target="#deleteHouseholdModal" :data-household-id="user.household.id" class="btn btn-danger w-100 fw-bolder mt-2">Delete Household</button>
      </div>
    </div>
  </form>
</template>

<script setup>
  import { useForm } from '@inertiajs/vue3'
  import { ref, computed, watch,} from 'vue'

  const props = defineProps({
    user: {
      type: Object,
      required: true
    },
  })

  const form = useForm({
    user_id: props.user.id,
    house_structure: props.user.household.house_structure,
    household_members: props.user.household.household_members,
    have_children: props.user.household.have_children !== null && props.user.household.have_children !== undefined ?
     (props.user.household.have_children ? 'true' : 'false') : '',
    number_of_children: props.user.household.number_of_children,
    has_other_pets: props.user.household.has_other_pets!== null && props.user.household.has_other_pets !== undefined ?
     (props.user.household.has_other_pets ? 'true' : 'false') : '',
    current_pets:props.user.household.current_pets,
    number_of_current_pets: props.user.household.number_of_current_pets
  })

  const houseStructureIsValid = ref(null)
  const houseStructureErrorMessage = ref('')

  const houseStructureValidationClass = computed(() => {
    if(houseStructureIsValid.value === true) return 'is-valid'
    if(houseStructureIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateHouseStructure(){
    const house_structure = form.house_structure.trim()
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

  const householdMembersIsValid = ref(null)
  const householdMembersErrorMessage = ref('')

  const householdMembersValidationClass = computed(() => {
    if(householdMembersIsValid.value === true) return 'is-valid'
    if(householdMembersIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateHouseholdMembers(){
    const household_members = form.household_members

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

  const haveChilrenIsValid = ref(null)
  const haveChildrenErrorMessage = ref('')

  const haveChildrenValidationClass = computed(() => {
    if(haveChilrenIsValid.value === true) return 'is-valid'
    if(haveChilrenIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateHaveChildren(){
    const have_children = form.have_children

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
    const number = form.number_of_children

    if(!number){
      numberOfChildrenIsValid.value = false
      numberOfChildrenErrorMessage.value = "Number of Children is required."
      return false
    }

    numberOfChildrenIsValid.value = true
    numberOfChildrenErrorMessage.value = ''
    return true
  }

  const hasOtherPetsIsValid = ref(null)
  const hasOtherPetsErrorMessage = ref('')

  const hasOtherPetsValidationClass = computed(() => {
    if(hasOtherPetsIsValid.value === true) return 'is-valid'
    if(hasOtherPetsIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateHasOtherPets(){
    const has_other_pets = form.has_other_pets

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
    const current_pets = form.current_pets.trim()
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

    const number_of_pets = form.number_of_current_pets

    if(!number_of_pets){
      numberOfCurrentPetsIsValid.value = false
      numberOfCurrentPetsErrorMessage.value = "Number of current pets is required."
      return false
    }

    numberOfCurrentPetsIsValid.value = true
    numberOfCurrentPetsErrorMessage.value = ''
    return true
  }

  watch(() => form.have_children, (newVal) => {
    if(newVal === 'false'){
      form.number_of_children = ""
    }
  })

  watch(() => form.has_other_pets, (newVal) => {
    if(newVal === 'false'){
      form.current_pets = ""
      form.number_of_current_pets = ""
    }
  })
  function submitForm() {

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

    form.put(`/households/${props.user.household.id}`,{
      preserveScroll: false,
      preserveState: false,
      onError: (errors) => {
        console.error("Validation errors:", errors)
      }
    })
  }
</script>