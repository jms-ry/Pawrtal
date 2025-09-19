<template>
  <div class="modal fade me-2" id="updateInKindDonationModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="updateInKindDonationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-sm-scrollable modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-info-subtle font-monospace">
          <i class="bi bi-box-seam-fill me-2 text-primary fs-2"></i>
          <h5 class="modal-title"><strong>Update In-kind Donation!</strong></h5>
        </div>
        <form @submit.prevent="submitForm">
          <div class="modal-body bg-info-subtle border-0" id="donationModalBody">
            <input type="hidden" name="user_id" class="form-control" v-model="form.user_id">
            <input type="hidden" name="donation_type" class="form-control" v-model="form.donation_type">
            <input type="hidden" name="status" class="form-control" v-model="form.status">
            <div class="mb-4">
              <div class="row g-2 mt-2">
                <div class="col-12 col-md-6 form-floating">
                  <input type="text" name="item_description" class="form-control" :class="itemDescriptionValidationClass" @blur="validateItemDescription" placeholder="Item Description" aria-label="Item Description" v-model="form.item_description">
                  <label class="form-label fw-bold">Item Description</label>
                  <small class="invalid-feedback fw-bold">{{ itemDescriptionErrorMessage }}</small>
                </div>
                <div class="col-12 col-md-6 form-floating">
                  <input type="number" nam="item_quantity" class="form-control" :class="itemQuantityValidationClass" @blur="validateItemQuantity" min="1" placeholder="Item Quantity" aria-label="Item Quantity" v-model="form.item_quantity">
                  <label class="form-label fw-bold">Item Quantity</label>
                  <small class="invalid-feedback fw-bold">{{ itemQuantityErrorMessage }}</small>
                  </div>
                </div>
              <div class="row g-2 mt-3">
                <div class="col-12 col-md-6 form-floating">
                  <input type="text" name="pick_up_location" class="form-control" :class="pickUpLocationValidationClass" @blur="validatePickUpLocation" placeholder="Pick up Location" aria-label="Pick up Location" v-model="form.pick_up_location">
                  <label class="form-label fw-bold">Pick up Location</label>
                  <small class="invalid-feedback fw-bold">{{ pickUpLocationErrorMessage }}</small>
                </div>
                <div class="col-12 col-md-6 form-floating">
                  <input type="text" name="contact_person" class="form-control" :class="contactPersonValidationClass" @blur="validateContactPerson" placeholder="Contact Person" aria-label="Contact Person" v-model="form.contact_person">
                  <label class="form-label fw-bold">Contact Person</label>
                  <small class="invalid-feedback fw-bold">{{ contactPersonErrorMessage }}</small>
                </div>
              </div>
              <div class="row g-2 mt-3">
                <div class="col-12">
                  <label class="form-label fw-bold">Upload Donation Image (Proof)</label>
                  <input  type="file" name="donation_image" id="donation-image" class="form-control" accept="image/*" @change="handleImageChange">
                  <small class="invalid-feedback fw-bold">{{ imageErrorMessage }}</small>
                  <div v-show="!imageErrorMessage">
                    <small class="text-muted mt-3">Leave blank to keep existing image</small>
                    <div class="mb-2 mt-2 justify-content-center">
                      <img :src="donationImage" class="w-100 h-100 object-fit-cover rounded-4" style="max-height: 300px; max-width: 100%;">
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer bg-info-subtle d-flex justify-content-end">
            <div>
              <button class="btn btn-primary me-1" type="submit">Update Donation</button>
              <button class="btn btn-danger" type="button" @click="closeModal">Close</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { useForm } from '@inertiajs/vue3'
  import { ref, onMounted, computed} from 'vue'
  import { Modal } from 'bootstrap'
  const props = defineProps({
    user: {
      type: Object,
      default: () => null
    },
  })

  const donationId = ref(null)
  const donationStatus = ref(null)
  const donationItemDescription = ref(null)
  const donationItemQuantity = ref(null)
  const pickUpLocation = ref(null)
  const contactPerson = ref(null)
  const type = ref(null)
  const donationImage = ref(null)

  const form = useForm({
    user_id: '',
    donation_type: '',
    status: '',
    item_description:'',
    item_quantity: '',
    pick_up_location: '',
    contact_person:'',
    donation_image: null,
    _method:'PUT'
  })

  function initializeForm(){
    form.user_id = props.user?.id
    form.donation_type = type.value
    form.status = donationStatus.value
    form.item_description = donationItemDescription.value
    form.item_quantity = donationItemQuantity.value
    form.pick_up_location = pickUpLocation.value
    form.contact_person = contactPerson.value
    form.donation_image = null
  }
  onMounted(() => {
    const modalEl = document.getElementById('updateInKindDonationModal')

    modalEl.addEventListener('show.bs.modal', (event) => { 
      const button = event.relatedTarget

      donationId.value = button.getAttribute('data-donation-id')
      donationItemDescription.value = button.getAttribute('data-donation-item-description')
      donationItemQuantity.value = button.getAttribute('data-donation-item-quantity')
      pickUpLocation.value = button.getAttribute('data-donation-pick-up-location')
      contactPerson.value = button.getAttribute('data-donation-contact-person')
      donationStatus.value = button.getAttribute('data-donation-status')
      type.value = button.getAttribute('data-donation-type')
      donationImage.value = button.getAttribute('data-donation-image')

      initializeForm()
    })
  })

  const imageErrorMessage = ref('')
  function handleImageChange(event) {
    const file = event.target.files[0]
    const input = document.getElementById('donation-image')
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
    form.donation_image = file
    input.classList.remove('is-invalid')
    input.classList.add('is-valid')
    imageErrorMessage.value = ''
    return true
  }

  const itemDescriptionIsValid = ref(null)
  const itemDescriptionErrorMessage = ref('')

  const itemDescriptionValidationClass = computed(() => {
    if(itemDescriptionIsValid.value === true) return 'is-valid'
    if(itemDescriptionIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateItemDescription(){
    const description = form.item_description.trim()
    const regex = /^[A-Za-z\s.,'"\-()\/&]{3,100}$/

    if (!description) {
      itemDescriptionIsValid.value = false
      itemDescriptionErrorMessage.value = "Item Description is required."
      return false
    }

    if (description.length < 4) {
      itemDescriptionIsValid.value = false
      itemDescriptionErrorMessage.value = "Please input a longer description."
      return false
    }

    if (!regex.test(description)) {
      itemDescriptionIsValid.value = false
      itemDescriptionErrorMessage.value = "Only letters and punctuation allowed, min 3 characters."
      return false
    }

    itemDescriptionIsValid.value = true
    itemDescriptionErrorMessage.value = ''
    return true
  }

  const itemQuantityIsValid = ref(null)
  const itemQuantityErrorMessage = ref()

  const itemQuantityValidationClass = computed(() => {
    if(itemQuantityIsValid.value === true) return 'is-valid'
    if(itemQuantityIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateItemQuantity(){
    const quantity = form.item_quantity

    if(!quantity){
      itemQuantityIsValid.value = false
      itemQuantityErrorMessage.value = "Item Quantity is required."
      return false
    }

    itemQuantityIsValid.value = true
    itemQuantityErrorMessage.value = ''
    return true
  }

  const pickUpLocationIsValid = ref(null)
  const pickUpLocationErrorMessage = ref('')

  const pickUpLocationValidationClass = computed(() => {
    if(pickUpLocationIsValid.value === true) return 'is-valid'
    if(pickUpLocationIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validatePickUpLocation(){
    const location = form.pick_up_location.trim()
    const regex = /^[\p{L}0-9\s.,#'"\-()/]{5,100}$/u
    if(!location){
      pickUpLocationIsValid.value = false
      pickUpLocationErrorMessage.value = "Pick up location is required."
      return false
    }

    if (!regex.test(location)) {
      pickUpLocationIsValid.value = false
      pickUpLocationErrorMessage.value = "Enter a valid location (letters, numbers, spaces, and basic punctuation, min 5 characters)."
      return false
    }

    pickUpLocationIsValid.value = true
    pickUpLocationErrorMessage.value = ""
    return true
  }

  const contactPersonIsValid = ref(null)
  const contactPersonErrorMessage = ref('')

  const contactPersonValidationClass = computed(() => {
    if(contactPersonIsValid.value === true) return 'is-valid'
    if(contactPersonIsValid.value === false) return 'is-invalid'
    return ''
  })

  function validateContactPerson(){
    const contact = form.contact_person.trim()
    const regex = /^[\p{L}\s.'\-]{2,50}$/u

    if(!contact){
      contactPersonIsValid.value = false
      contactPersonErrorMessage.value = "Contact Person is required."
      return false
    }

    if (!regex.test(contact)) {
      contactPersonIsValid.value = false
      contactPersonErrorMessage.value = "Enter a valid name (letters, spaces, and basic punctuation only, 2–50 characters)."
      return false
    }

    contactPersonIsValid.value = true
    contactPersonErrorMessage.value = ""
    return true

  }

  function submitForm() {
    // if (!donationId.value) {
    //   console.error('No ID available')
    //   return
    // }

    const formData = new FormData()

    formData.append('_method', 'PUT')
    formData.append('user_id',form.user_id || '')
    formData.append('donation_type',form.donation_type || '')
    formData.append('status',form.status || '')
    formData.append('item_description',form.item_description || '')
    formData.append('item_quantity',form.item_quantity || '')
    formData.append('pick_up_location',form.pick_up_location || '')
    formData.append('contact_person',form.contact_person || '')

    if (form.donation_image instanceof File) {
      formData.append('image', form.donation_image)
    }

    const isItemDescriptionValid = validateItemDescription()
    const isItemQuantityValid = validateItemQuantity()
    const isPickUpLocationValid = validatePickUpLocation()
    const isContactPersonValid = validateContactPerson()
    const isDonationImageInvalid = imageErrorMessage.value

    if(!isItemDescriptionValid || !isItemQuantityValid || !isPickUpLocationValid || !isContactPersonValid || isDonationImageInvalid){
      return
    }
    
    form.post(`/donations/${donationId.value}`, {
      data:formData,
      forceFormData:true,
      preserveScroll: false,
      preserveState: false,
      onSuccess: () => {
        closeModal()
      },
    })
  }

  function closeModal(){
    const modalEl = document.getElementById('updateInKindDonationModal')
    const modal = Modal.getInstance(modalEl)
    if (modal) {
      modal.hide()
    }

    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove())
    document.body.classList.remove('modal-open')
    document.body.style.removeProperty('overflow')
    document.body.style.removeProperty('padding-right')
    
    donationId.value = null
    donationStatus.value = null
    donationImage.value = null
    donationItemDescription.value = null
    donationItemQuantity.value = null
    pickUpLocation.value = null
    contactPerson.value = null
    type.value = null 

    itemDescriptionErrorMessage.value = ''
    itemDescriptionIsValid.value = null
    itemQuantityErrorMessage.value = ''
    itemQuantityIsValid.value = null
    pickUpLocationErrorMessage.value = ''
    pickUpLocationIsValid.value = null
    contactPersonErrorMessage.value = ''
    contactPersonIsValid.value = null

    imageErrorMessage.value = ''
    const fileInput = document.getElementById('donation-image')
    if (fileInput) {
      fileInput.value = ''
      fileInput.classList.remove('is-valid', 'is-invalid')
    }
  }
</script>