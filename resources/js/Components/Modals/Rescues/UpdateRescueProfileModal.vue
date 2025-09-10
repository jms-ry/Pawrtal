<template>
  <div class="modal fade me-2" id="editRescueProfileModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editRescueProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-sm-scrollable modal-lg">
      <div class="modal-content ">
        <div class="modal-header bg-info-subtle">
          <i class="bi bi-plus-circle-fill me-3 text-primary fs-2"></i>
          <h5 class="modal-title">Update {{ rescue.name_formatted }}'s Profile</h5>
        </div>
        <form @submit.prevent="submitForm">
          <div class="modal-body bg-info-subtle border-0">
            <div class="row g-2 mt-2">
              <div class="col-12 col-md-4 form-floating">
                <input type="text" name="name" class="form-control" placeholder="Rescue name" aria-label="Rescue name" id="floating_rescue_name" autocomplete="true" v-model="form.name" required>
                <label for="floating_rescue_name" class="form-label fw-bold">Name</label>
              </div>
              <div class="col-12 col-md-4 form-floating">
                <input type="text" name="species" id="floating_rescue_species" class="form-control" placeholder="Rescue name" aria-label="Rescue name" autocomplete="true" v-model="form.species" required>
                <label for="floating_rescue_species" class="form-label fw-bold">Species</label>
              </div>
              <div class="col-12 col-md-4 form-floating">
                <input type="text" name="breed" class="form-control" placeholder="Rescue breed" aria-label="Rescue breed" id="floating_rescue_breed" v-model="form.breed">
                <label for="floating_rescue_breed" class="form-label fw-bold">Breed</label>
              </div>
            </div>

            <div class="row g-2 mt-2">
              <div class="col-12 col-md-4 form-floating">
                <select v-model="form.sex" name="sex" id="floating_sex" class="form-select" aria-label="sex-select" required>
                  <option value="" hidden  >Sex</option>
                  <option value="male" >Male</option>
                  <option value="female" >Female</option>
                </select>
                <label for="floating_sex" class="form-label fw-bold">Select a sex</label>
              </div>
              <div class="col-12 col-md-4 form-floating">
                <input type="text" name="age" class="form-control" placeholder="Rescue age" aria-label="Rescue age" id="floating_rescue_age" v-model="form.age">
                <label for="floating_rescue_age" class="form-label fw-bold">Age (e.g 6 months old)</label>
              </div>
              <div class="col-12 col-md-4 form-floating">
                <select v-model="form.size" name="size" id="floating_rescue_size" class="form-select" aria-label="size-select" required>
                  <option value="" hidden >Size</option>
                  <option value="small" >Small</option>
                  <option value="medium" >Medium</option>
                  <option value="large" >Large</option>
                </select>
                <label for="floating_rescue_size" class="form-label fw-bold">Select size</label>
              </div>
            </div>

            <div class="row g-2 mt-2">
              <div class="col-12 col-md-4 form-floating">
                <input type="text" name="color" class="form-control" placeholder="Rescue color" aria-label="Rescue color" id="floating_rescue_color" v-model="form.color">
                <label for="floating_rescue_color" class="form-label fw-bold">Color</label>
              </div>
              <div class="col-12 col-md-4 form-floating">
                <input type="text" name="distinctive_features" class="form-control" placeholder="Rescue distinctive features" aria-label="Rescue distinctive features" id="floating_rescue_distinctive_features" v-model="form.distinctive_features">
                <label for="floating_rescue_distinctive_features" class="form-label fw-bold">Distinctive Features</label>
              </div>
              <div class="col-12 col-md-4 form-floating">
                <select v-model="form.health_status" name="health_status" id="floating_health_status" class="form-select" aria-label="health-status-select" required>
                  <option value="" hidden>Health Status</option>
                  <option value="healthy" >Healthy</option>
                  <option value="sick" >Sick </option>
                  <option value="injured" >Injured</option>
                </select>
                <label for="floating_health_status" class="form-label fw-bold">Select health status</label>
              </div>
            </div>

            <div class="row g-2 mt-2">
              <div class="col-12 col-md-4 form-floating">
                <select v-model="form.vaccination_status" name="vaccination_status" id="floating_vaccination_status" class="form-select" aria-label="vaccination-status-select" required>
                  <option value="" hidden>Vaccination Status</option>
                  <option value="vaccinated" >Vaccinated</option>
                  <option value="partially_vaccinated">Partially Vaccinated</option>
                  <option value="not_vaccinated" >Not Vaccinated</option>
                </select>
                <label for="floating_vaccination_status" class="form-label fw-bold">Select vaccination status</label>
              </div>
              <div class="col-12 col-md-4 form-floating">
                <select v-model="form.spayed_neutered" name="spayed_neutered" id="floating_spayed_neutered" class="form-select" aria-label="spayed-neutered-select" required>
                  <option value="" hidden>Spay/Neutered</option>
                  <option value="true" >Yes</option>
                  <option value="false">No</option>
                </select>
                <label for="floating_spayed_neutered" class="form-label fw-bold">Is it spayed/neutered?</label>
              </div>
              <div class="col-12 col-md-4 form-floating">
                <select v-model="form.adoption_status" name="adoption_status" id="floating_adoption_status" class="form-select" aria-label="adoption-status-select" required>
                  <option value="" hidden>Adoption Status</option>
                  <option value="available" >Available</option>
                  <option value="unavailable" >Unavailable</option>
                  <option value="adopted">Adopted</option>
                </select>
                <label for="floating_adoption_status" class="form-label fw-bold">Select adoption status</label>
              </div>
            </div>

            <div class="row g-2 mt-2">
              <div class="col-12 col-md-6">
                <label for="profile_image" class="form-label fw-bold">Change Profile Image</label>
                <input type="file" name="profile_image" id="profile_image" class="form-control" accept="image/*" @change="handleProfileImageChange">
                <small class="text-muted mt-3">Leave blank to keep existing image</small>
                <div v-if="rescue.profile_image" class="mb-2 mt-2">
                  <img :src="rescue.profile_image_url" :alt="rescue.name" id="rescueProfileImage" class="w-100 h-100 object-fit-cover rounded-4" style="max-height: 150px;">
                </div>
              </div>
              <div class="col-12 col-md-6">
                <label for="images" class="form-label fw-bold">Upload Additional Image/s</label>
                <input type="file" name="images[]" id="images" class="form-control" accept="image/*" multiple @change="handleImagesChange">
              </div>
            </div>

            <div class="row g-2 mt-2">
              <div class="col-12 form-floating">
                <textarea v-model="form.description" name="description" id="floating_rescue_description" class="form-control" placeholder="Rescue description" aria-label="Rescue description" style="height: 100px" required></textarea>
                <label for="floating_rescue_description" class="form-label fw-bold">Description</label>
              </div>
            </div>

          </div>

          <div class="modal-footer bg-info-subtle">
            <button class="btn btn-primary me-1" type="submit">Update Rescue Profile</button>
            <button class="btn btn-danger" type="button"  data-bs-dismiss="modal">Close</button>
          </div>

          <div v-if="form.errors && Object.keys(form.errors).length > 0" class="alert alert-danger m-3">
            <ul class="mb-0">
              <li v-for="(error, field) in form.errors" :key="field">
                <strong>{{ field }}:</strong> {{ Array.isArray(error) ? error[0] : error }}
              </li>
            </ul>
          </div>

        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { useForm } from '@inertiajs/vue3'
  import { Modal } from 'bootstrap'
  import { onMounted, watch } from 'vue'

  const props = defineProps({
    rescue: {
      type: Object,
    }
  })

  const form = useForm({
    name: '',
    species: '',
    breed: '',
    sex: '',
    age: '',
    size: '',
    color: '',
    distinctive_features: '',
    health_status: '',
    vaccination_status: '',
    spayed_neutered: '',
    adoption_status: '',
    description: '',
    profile_image: null,
    images: null,
    _method: 'PUT'
  })
  // Initialize form with rescue data
  onMounted(() => {
    initializeForm()
  })

  // Watch for changes in rescue prop
  watch(() => props.rescue, () => {
    initializeForm()
  }, { deep: true })

  function initializeForm() {
    if (props.rescue) {
      form.name = props.rescue.name || ''
      form.species = props.rescue.species || ''
      form.breed = props.rescue.breed || ''
      form.sex = props.rescue.sex || ''
      form.age = props.rescue.age || ''
      form.size = props.rescue.size || ''
      form.color = props.rescue.color || ''
      form.distinctive_features = props.rescue.distinctive_features || ''
      form.health_status = props.rescue.health_status || ''
      form.vaccination_status = props.rescue.vaccination_status || ''
      form.spayed_neutered = props.rescue.spayed_neutered !== null && props.rescue.spayed_neutered !== undefined 
        ? (props.rescue.spayed_neutered ? 'true' : 'false') 
      : ''
      form.adoption_status = props.rescue.adoption_status || ''
      form.description = props.rescue.description || ''
    
      // Reset file inputs
      form.profile_image = null
      form.images = null
    }
  }

  function handleProfileImageChange(event) {
    const file = event.target.files[0]
    if (file) {
      form.profile_image = file
    } else {
      form.profile_image = null
    }
  }

  function handleImagesChange(event) {
    const files = event.target.files
    if (files && files.length > 0) {
      form.images = files
    } else {
      form.images = null
    }
  }

  function submitForm() {
    const formData = new FormData()
  
    formData.append('_method', 'PUT')
  
    formData.append('name', form.name || '')
    formData.append('species', form.species || '')
    formData.append('breed', form.breed || '')
    formData.append('sex', form.sex || '')
    formData.append('age', form.age || '')
    formData.append('size', form.size || '')
    formData.append('color', form.color || '')
    formData.append('distinctive_features', form.distinctive_features || '')
    formData.append('health_status', form.health_status || '')
    formData.append('vaccination_status', form.vaccination_status || '')
    formData.append('spayed_neutered', form.spayed_neutered || '')
    formData.append('adoption_status', form.adoption_status || '')
    formData.append('description', form.description || '')
  
    if (form.profile_image instanceof File) {
      formData.append('profile_image', form.profile_image)
    }
  
    if (form.images && form.images.length > 0) {
      for (let i = 0; i < form.images.length; i++) {
        formData.append('images[]', form.images[i])
      }
    }
  
    form.post(`/rescues/${props.rescue.id}`, {
      data: formData,
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
    const modalEl = document.getElementById('editRescueProfileModal')
    const modal = Modal.getInstance(modalEl)
    if (modal) {
      modal.hide()
    }
    
    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove())
    document.body.classList.remove('modal-open')
    document.body.style.removeProperty('overflow')
    document.body.style.removeProperty('padding-right')
  }
</script>
