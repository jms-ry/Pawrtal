<template>
  <div class="modal fade" id="updateLostReportModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="updateReportModalLabel">
    <div class="modal-dialog modal-dialog-centered modal-dialog-sm-scrollable modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-info-subtle">
          <i class="bi bi-plus-circle-fill me-3 text-primary fs-2"></i>
          <h5 class="modal-title">Update <span id="modal-title-span"></span> Report!</h5>
        </div>
        <!--Lost Animal Report Form-->
        <form @submit.prevent="submitForm">
          <div class="modal-body bg-info-subtle border-0">
            <input type="hidden" name="user_id" class="form-control" v-model="form.user_id">
            <div class="row g-2 mt-2">
              <div class="col-12 col-md-4 form-floating">
                <input type="text" name="animal_name" v-model="form.animal_name" class="form-control" placeholder="Animal name" aria-label="Animal name" id="floating_animal_name" autocomplete="true" required>
                <label for="floating_animal_name" class="form-label fw-bold">Animal's Name</label>
              </div>
              <div class="col-12 col-md-4 form-floating">
                <input type="text" name="species" v-model="form.species" class="form-control" placeholder="Animal species" aria-label="Animal species" id="floating_animal_species" autocomplete="true" required>
                <label for="floating_animal_species" class="form-label fw-bold">Species (e.g Dog, Cat, etc.)</label>
              </div>
              <div class="col-12 col-md-4 form-floating">
                <input type="text" name="breed" v-model="form.breed" class="form-control" placeholder="Animal breed" aria-label="Animal breed" id="floating_animal_breed" autocomplete="true" required>
                <label for="floating_animal_breed" class="form-label fw-bold">Breed</label>
              </div>
            </div>

            <div class="row g-2 mt-2">
              <div class="col-12 col-md-4 form-floating">
                <input type="text" name="color" v-model="form.color" class="form-control" placeholder="Animal color" aria-label="Animal color" id="floating_animal_color" autocomplete="true" required>
                <label for="floating_animal_color" class="form-label fw-bold">Color</label>
              </div>
              <div class="col-12 col-md-4 form-floating">
                <select name="sex" v-model="form.sex" id="floating_animal_sex" class="form-select" aria-label="sex-select" required>
                  <option selected hidden>Sex</option>
                  <option value="male">Male</option>
                  <option value="female">Female</option>
                  <option value="unknown">Unknown</option>
                </select>
                <label for="floating_animal_sex" class="form-label fw-bold">Select a sex</label>
              </div>
              <div class="col-12 col-md-4 form-floating">
                <input type="text" v-model="form.age_estimate" name="age_estimate" class="form-control" placeholder="Animal age estimate" aria-label="Animal age estimate" id="floating_animal_age_estimate" autocomplete="true" required>
                <label for="floating_animal_age_estimate" class="form-label fw-bold">Age Estimate (e.g 6 months old)</label>
              </div>
            </div>

            <div class="row g-2 mt-2">
              <div class="col-12 col-md-4 form-floating">
                <select name="size" v-model="form.size" id="floating_animal_size" class="form-select" aria-label="size-select" required>
                  <option selected hidden>Size</option>
                  <option value="small">Small</option>
                  <option value="medium">Medium</option>
                  <option value="large">Large</option>
                </select>
                <label for="floating_animal_size" class="form-label fw-bold">Select size</label>
              </div>
              <div class="col-12 col-md-8 form-floating">
                <input type="text" v-model="form.distinctive_features" name="distinctive_features" class="form-control" placeholder="Animal distinctive features" aria-label="Animal distinctive features" id="floating_animal_distinctive_features">
                <label for="floating_animal_distinctive_features" class="form-label fw-bold">Distinctive Features</label>
              </div>
            </div>

            <div class="row g-2 mt-2">
              <div class="col-12 col-md-8 form-floating">
                <input type="text" v-model="form.last_seen_location" name="last_seen_location" class="form-control" placeholder="Animal last seen location" aria-label="Animal last seen location" id="floating_animal_last_seen_location" required>
                <label for="floating_animal_last_seen_location" class="form-label fw-bold">Last Seen Location</label>
              </div>
              <div class="col-12 col-md-4 form-floating">
                <input type="date" v-model="form.last_seen_date" name="last_seen_date" class="form-control" placeholder="Animal last seen date" aria-label="Animal last seen date"  id="floating_animal_last_seen_date" required>
              <label for="floating_animal_last_seen_date" class="form-label fw-bold">Last Seen Date</label>
              </div>
            </div>

            <div class="row g-2 mt-2">
              <div class="col-md-8 col-12">
                <label for="image" class="form-label fw-bold">Update Image</label>
                <input type="file" name="image" id="image" class="form-control" accept="image/*" @change="handleImageChange">
                <small class="text-muted mt-3">Leave blank to keep existing image</small>
                <div class="mb-2 mt-2">
                  <img id="reportImage" class="w-100 h-100 object-fit-cover rounded-4" style="max-height: 150px;">
                </div>
              </div>
              <div class="col-md-4 col-12">
                <label for="report_status" class="form-label fw-bold">Update Report Status</label>
                <select v-model="form.status" name="status" id="report_status" class="form-select" aria-label="size-select" required>
                  <option selected hidden>Status</option>
                  <option value="resolved">Resolved</option>
                  <option value="active">Not yet resolved</option>
                </select>
              </div>
            </div>
          </div>
          <div class="modal-footer bg-info-subtle">
            <button class="btn btn-primary me-1" type="submit">Update Report</button>
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
  import { ref, onMounted } from 'vue'

  const props = defineProps({
    user: {
      type: Object,
    },
  })

  const reportId = ref(null)
  const reportAnimalName = ref(null)
  const reportAnimalSpecies = ref(null)
  const reportAnimalBreed = ref(null)
  const reportAnimalColor = ref(null)
  const reportAnimalSex = ref(null)
  const reportAnimalAgeEstimate = ref(null)
  const reportAnimalSize = ref(null)
  const reportAnimalDistinctiveFeatures = ref(null)
  const reportAnimalLastSeenLocation = ref(null)
  const reportAnimalLastSeenDate = ref(null)
  const reportAnimalStatus = ref(null)

  const form = useForm({
    animal_name: '',
    species: '',
    breed: '',
    color:'',
    sex: '',
    age_estimate:'',
    size:'',
    distinctive_features:'',
    last_seen_location: '',
    last_seen_date:'',
    status:'',
    image:null,
    type:'lost',
    user_id:'',
    _method:'PUT'
  })

  function initializeForm(){
    form.animal_name = reportAnimalName || ''
    form.species = reportAnimalSpecies || ''
    form.breed = reportAnimalBreed || ''
    form.color = reportAnimalColor || ''
    form.sex = reportAnimalSex || ''
    form.age_estimate = reportAnimalAgeEstimate || ''
    form.size = reportAnimalSize || ''
    form.distinctive_features = reportAnimalDistinctiveFeatures || ''
    form.last_seen_location = reportAnimalLastSeenLocation || ''
    form.last_seen_date = reportAnimalLastSeenDate ||''
    form.status = reportAnimalStatus || ''
    form.user_id = props.user?.id || ''
    form.image = null
  }

  onMounted(() => {
    const modalEl = document.getElementById('updateLostReportModal')
    
    modalEl.addEventListener('show.bs.modal', (event) => {
      const button = event.relatedTarget  
      
      reportId.value = button.getAttribute('data-report-id')

      reportAnimalName.value = button.getAttribute('data-report-animal-name')

      reportAnimalSpecies.value = button.getAttribute('data-report-species')

      reportAnimalBreed.value = button.getAttribute('data-report-breed')

      reportAnimalColor.value = button.getAttribute('data-report-color')

      reportAnimalSex.value = button.getAttribute('data-report-sex')

      reportAnimalAgeEstimate.value = button.getAttribute('data-report-age-estimate')

      reportAnimalSize.value = button.getAttribute('data-report-size')

      reportAnimalDistinctiveFeatures.value = button.getAttribute('data-report-distinctive-features')

      reportAnimalLastSeenLocation.value = button.getAttribute('data-report-location')

      reportAnimalLastSeenDate.value = button.getAttribute('data-report-last-seen-date')

      reportAnimalStatus.value = button.getAttribute('data-report-status')

      initializeForm()

    })

    
  })

  function handleImageChange(event) {
    const file = event.target.files[0]
    if (file) {
      form.image = file
    } else {
      form.image = null
    }
  }

  function submitForm(){
    const formData = new FormData()

    formData.append('_method', 'PUT')
    formData.append('animal_name', form.animal_name || '')
    formData.append('species', form.species || '')
    formData.append('breed', form.breed || '')
    formData.append('color', form.color || '')
    formData.append('sex', form.sex|| '')
    formData.append('age_estimate', form.age_estimate || '')
    formData.append('size', form.size || '')
    formData.append('distinctive_features', form.distinctive_features || '')
    formData.append('last_seen_location', form.last_seen_location|| '')
    formData.append('last_seen_date', form.last_seen_date || '')
    formData.append('status', form.status || '')
    formData.append('type','lost')
    formData.append('user_id',form.user_id)

    if (form.image instanceof File) {
      formData.append('image', form.image)
    }

    form.post(`/reports/${reportId.value}`,{
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
  function closeModal(){
    const modalEl = document.getElementById('updateLostReportModal')
    const modal = Modal.getInstance(modalEl)
    if (modal) {
      modal.hide()
    }

    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove())
    document.body.classList.remove('modal-open')
    document.body.style.removeProperty('overflow')
    document.body.style.removeProperty('padding-right')
    
    reportId.value = null
    reportAnimalName.value = null
    reportAnimalSpecies.value = null
    reportAnimalBreed.value = null
    reportAnimalColor.value = null
    reportAnimalSex.value = null
    reportAnimalAgeEstimate.value = null
    reportAnimalSize.value = null
    reportAnimalDistinctiveFeatures.value = null
    reportAnimalLastSeenLocation.value = null
    reportAnimalLastSeenDate.value = null
    reportAnimalStatus.value = null

    form.reset()
  }
  
</script>