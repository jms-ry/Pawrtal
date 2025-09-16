<template>
  <div class="modal fade me-2" id="createInKindDonationModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="createInKindDonationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-sm-scrollable modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-info-subtle font-monospace">
          <i class="bi bi-box-seam-fill me-2 text-primary fs-2"></i>
          <h5 class="modal-title"><strong>Make In-kind Donation for the rescues!</strong></h5>
        </div>
        <form @submit.prevent="submitForm">
          <div class="modal-body bg-info-subtle border-0" id="donationModalBody">
            <input type="hidden" name="user_id" class="form-control" :value="user?.id">
            <input type="hidden" name="donation_type" class="form-control" value="in-kind">
            <input type="hidden" name="status" class="form-control" value="pending">
            
            <!-- Loop through donation items -->
            <div v-for="(item, index) in donationItems" :key="item.id" class="donation-item-wrapper">
              <hr v-if="index > 0" class="mt-4">
              <div class="mb-4">
                <div class="row g-2 mt-2">
                  <div class="col-12 col-md-6 form-floating">
                    <input type="text" :class="getValidationClass(item.validation.itemDescription)" @blur="validateItemDescription(index)" v-model="item.itemDescription" class="form-control" placeholder="Item Description" aria-label="Item Description">
                    <label class="form-label fw-bold">Item Description</label>
                    <small class="invalid-feedback fw-bold">{{ item.errors.itemDescription }}</small>
                  </div>
                  <div class="col-12 col-md-6 form-floating">
                    <input type="number" :class="getValidationClass(item.validation.itemQuantity)" @blur="validateItemQuantity(index)" v-model="item.itemQuantity"
                      class="form-control" min="1" placeholder="Item Quantity" aria-label="Item Quantity">
                    <label class="form-label fw-bold">Item Quantity</label>
                    <small class="invalid-feedback fw-bold">{{ item.errors.itemQuantity }}</small>
                  </div>
                </div>
                <div class="row g-2 mt-3">
                  <div class="col-12 col-md-6 form-floating">
                    <input type="text" :class="getValidationClass(item.validation.pickUpLocation)" @blur="validatePickUpLocation(index)" v-model="item.pickUpLocation" class="form-control" placeholder="Pick up Location" aria-label="Pick up Location">
                    <label class="form-label fw-bold">Pick up Location</label>
                    <small class="invalid-feedback fw-bold">{{ item.errors.pickUpLocation }}</small>
                  </div>
                  <div class="col-12 col-md-6 form-floating">
                    <input type="text" :class="getValidationClass(item.validation.contactPerson)" @blur="validateContactPerson(index)" v-model="item.contactPerson" class="form-control" placeholder="Contact Person" aria-label="Contact Person">
                    <label class="form-label fw-bold">Contact Person</label>
                    <small class="invalid-feedback fw-bold">{{ item.errors.contactPerson }}</small>
                  </div>
                </div>
                <div class="row g-2 mt-3">
                  <div class="col-12">
                    <label class="form-label fw-bold">Upload Donation Image (Proof)</label>
                    <input  type="file" :class="getValidationClass(item.validation.donationImage)" class="form-control donation-image" accept="image/*"  @change="validateDonationImage($event, index)">
                    <div class="form-text">Please upload a clear photo of the donation item(s) as proof.</div>
                    <small class="invalid-feedback fw-bold">{{ item.errors.donationImage }}</small>
                  </div>
                </div>
                <button  v-if="index > 0" type="button" class="btn btn-danger mt-3" @click="removeDonationItem(index)">
                  Remove Donation Item
                </button>
              </div>
            </div>
          </div>
          
          <!--Large Screen-->
          <div class="modal-footer bg-info-subtle d-none d-md-flex justify-content-between">
            <div>
              <button class="btn btn-info me-1" type="button" @click="addDonationItem">New Donation Item</button>
            </div>
            <div>
              <button class="btn btn-primary me-1" type="submit">Submit Donation</button>
              <button class="btn btn-danger" type="button" @click="closeModal">Close</button>
            </div>
          </div>
          <!--Small Screen-->
          <div class="modal-footer bg-info-subtle d-md-none d-flex justify-content-center">
            <div>
              <button class="btn btn-subtle-outline-primary me-1" type="button" @click="addDonationItem">New Donation Item</button>
            </div>
            <div>
              <button class="btn btn-primary me-1" type="submit">Submit Donation</button>
              <button class="btn btn-danger" type="button" @click="closeModal">Close</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { router } from '@inertiajs/vue3'
  import { Modal } from 'bootstrap'
  import { ref} from 'vue'

  const props = defineProps({
    user: {
      type: Object,
      default: () => null
    }
  })

  function createDonationItem() {
    return {
      id: Date.now() + Math.random(),
      itemDescription: '',
      itemQuantity: '',
      pickUpLocation: '',
      contactPerson: '',
      donationImage: null,
      validation: {
        itemDescription: null,
        itemQuantity: null,
        pickUpLocation: null,
        contactPerson: null,
        donationImage: null
      },
      errors: {
        itemDescription: '',
        itemQuantity: '',
        pickUpLocation: '',
        contactPerson: '',
        donationImage: ''
      }
    }
  }

  const donationItems = ref([createDonationItem()])

  function addDonationItem() {
    donationItems.value.push(createDonationItem())
  }

  function removeDonationItem(index) {
    donationItems.value.splice(index, 1)
  }

  // Get validation class helper
  function getValidationClass(validationState) {
    if (validationState === true) return 'is-valid'
    if (validationState === false) return 'is-invalid'
    return ''
  }

  // Validation functions
  function validateItemDescription(index) {
    const item = donationItems.value[index]
    const description = item.itemDescription.trim()
    const regex = /^[A-Za-z\s.,'"\-()\/&]{3,100}$/

    if (!description) {
      item.validation.itemDescription = false
      item.errors.itemDescription = "Item Description is required."
      return false
    }

    if (description.length < 4) {
      item.validation.itemDescription = false
      item.errors.itemDescription = "Please input a longer description."
      return false
    }

    if (!regex.test(description)) {
      item.validation.itemDescription = false
      item.errors.itemDescription = "Only letters and punctuation allowed, min 3 characters."
      return false
    }

    item.validation.itemDescription = true
    item.errors.itemDescription = ''
    return true
  }

  function validateItemQuantity(index) {
    const item = donationItems.value[index]
    const quantity = item.itemQuantity

    if (!quantity) {
      item.validation.itemQuantity = false
      item.errors.itemQuantity = "Item Quantity is required."
      return false
    }

    item.validation.itemQuantity = true
    item.errors.itemQuantity = ""
    return true
  }

  function validatePickUpLocation(index) {
    const item = donationItems.value[index]
    const location = item.pickUpLocation.trim()
    const regex = /^[\p{L}0-9\s.,#'"\-()/]{5,100}$/u

    if (!location) {
      item.validation.pickUpLocation = false
      item.errors.pickUpLocation = "Pick up location is required."
      return false
    }

    if (!regex.test(location)) {
      item.validation.pickUpLocation = false
      item.errors.pickUpLocation = "Enter a valid location (letters, numbers, spaces, and basic punctuation, min 5 characters)."
      return false
    }

    item.validation.pickUpLocation = true
    item.errors.pickUpLocation = ""
    return true
  }

  function validateContactPerson(index) {
    const item = donationItems.value[index]
    const contact = item.contactPerson.trim()
    const regex = /^[\p{L}\s.'\-]{2,50}$/u

    if (!contact) {
      item.validation.contactPerson = false
      item.errors.contactPerson = "Contact Person is required."
      return false
    }

    if (!regex.test(contact)) {
      item.validation.contactPerson = false
      item.errors.contactPerson = "Enter a valid name (letters, spaces, and basic punctuation only, 2–50 characters)."
      return false
    }

    item.validation.contactPerson = true
    item.errors.contactPerson = ""
    return true
  }

  function validateDonationImage(event, index) {
    const item = donationItems.value[index]
    const file = event.target.files[0]

    if (!file) {
      item.validation.donationImage = false
      item.errors.donationImage = "Donation Image is required."
      return false
    }

    const allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx']
    const fileExtension = file.name.split('.').pop().toLowerCase()
    
    if (!allowedExtensions.includes(fileExtension)) {
      item.validation.donationImage = false
      item.errors.donationImage = "Only JPG, JPEG, PNG, PDF, DOC, and DOCX are allowed."
      return false
    }

    if (file.size > 2 * 1024 * 1024) {
      item.validation.donationImage = false
      item.errors.donationImage = "File must not exceed 2MB."
      return false
    }

    item.donationImage = file
    item.validation.donationImage = true
    item.errors.donationImage = ""
    return true
  }

  function submitForm(event) {
    event.preventDefault()
    
    let allValid = true
    donationItems.value.forEach((item, index) => {
      const descValid = validateItemDescription(index)
      const qtyValid = validateItemQuantity(index)
      const locValid = validatePickUpLocation(index)
      const contactValid = validateContactPerson(index)
      
      if (!item.donationImage) {
        item.validation.donationImage = false
        item.errors.donationImage = "Donation Image is required."
      }
      
      const imageValid = item.validation.donationImage === true
      
      if (!descValid || !qtyValid || !locValid || !contactValid || !imageValid) {
        allValid = false
      }
    })

    if (!allValid) {
      return
    }

    const formData = new FormData()
    formData.append('user_id', props.user?.id || '')
    formData.append('donation_type', 'in-kind')
    formData.append('status', 'pending')

    donationItems.value.forEach((item) => {
      formData.append('item_description[]', item.itemDescription)
      formData.append('item_quantity[]', item.itemQuantity)
      formData.append('pick_up_location[]', item.pickUpLocation)
      formData.append('contact_person[]', item.contactPerson)
      if (item.donationImage) {
        formData.append('donation_image[]', item.donationImage)
      }
    })

    router.post('/donations', formData, {
      forceFormData: true,
      preserveScroll: false,
      preserveState: false,
      onSuccess: () => {
        closeModal()
      },
      onError: (errors) => {
        console.error("Validation errors:", errors)
      }
    })
  }

  function closeModal() {
    const modalEl = document.getElementById('createInKindDonationModal')
    const modal = Modal.getInstance(modalEl)
    if (modal) {
      modal.hide()
    }

    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove())
    document.body.classList.remove('modal-open')
    document.body.style.removeProperty('overflow')
    document.body.style.removeProperty('padding-right')
    
    donationItems.value = [createDonationItem()]
  }
</script>