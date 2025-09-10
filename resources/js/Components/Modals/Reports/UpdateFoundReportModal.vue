<template>
  <div class="modal fade" id="updateFoundReportModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="updateReportModalLabel">
    <div class="modal-dialog modal-dialog-centered modal-dialog-sm-scrollable modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-info-subtle">
          <i class="bi bi-plus-circle-fill me-3 text-primary fs-2"></i>
          <h5 class="modal-title">Update <span id="found-modal-title-span"></span> Report!</h5>
        </div>
        <!--Found Animal Report Form-->
        <form @submit.prevent="submitForm">
          <div class="modal-body bg-info-subtle border-0">
            <input type="hidden" name="user_id" class="form-control" v-model="form.user_id">
            <div class="row g-2 mt-2">
              <div class="col-12 col-md-4 form-floating">
                <input type="text" v-model="form.species" name="species" class="form-control" placeholder="Animal species" aria-label="Animal species" id="floating_animal_species_found" autocomplete="true" required>
                <label for="floating_animal_species_found" class="form-label fw-bold">Species (e.g Dog, Cat, etc.)</label>
              </div>
              <div class="col-12 col-md-4 form-floating">
                <input type="text" name="breed" v-model="form.breed" class="form-control" placeholder="Animal breed" aria-label="Animal breed" id="floating_animal_breed_found" autocomplete="true" required>
                <label for="floating_animal_breed_found" class="form-label fw-bold">Breed</label>
              </div>
              <div class="col-12 col-md-4 form-floating">
                <input type="text" name="color" v-model="form.color" class="form-control" placeholder="Animal color" aria-label="Animal color" id="floating_animal_color_found" autocomplete="true" required>
                <label for="floating_animal_color_found" class="form-label fw-bold">Color</label>
              </div>
            </div>

            <div class="row g-2 mt-2">
              <div class="col-12 col-md-4 form-floating">
                <select name="sex" v-model="form.sex" id="floating_animal_sex_found" class="form-select" aria-label="sex-select" required>
                  <option selected hidden>Sex</option>
                  <option value="male">Male</option>
                  <option value="female">Female</option>
                  <option value="unknown">Unknown</option>
                </select>
                <label for="floating_animal_sex_found" class="form-label fw-bold">Select a sex</label>
              </div>
              <div class="col-12 col-md-4 form-floating">
                <input type="text" name="age_estimate" v-model="form.age_estimate" class="form-control" placeholder="Animal age estimate" aria-label="Animal age estimate" id="floating_animal_age_estimate_found" autocomplete="true" required>
                <label for="floating_animal_age_estimate_found" class="form-label fw-bold">Age Estimate (e.g 6 months old)</label>
              </div>
              <div class="col-12 col-md-4 form-floating">
                <select name="size" id="floating_animal_size_found" v-model="form.size" class="form-select" aria-label="size-select" required>
                  <option selected hidden>Size</option>
                  <option value="small">Small</option>
                  <option value="medium">Medium</option>
                  <option value="large">Large</option>
                </select>
                <label for="floating_animal_size_found" class="form-label fw-bold">Select size</label>
              </div>
            </div>

            <div class="row g-2 mt-2">
              <div class="col-12 col-md-4 form-floating">
                <input type="text" name="distinctive_features" v-model="form.distinctive_features" class="form-control" placeholder="Animal distinctive features" aria-label="Animal distinctive features" id="floating_animal_distinctive_features_found">
                <label for="floating_animal_distinctive_features_found" class="form-label fw-bold">Distinctive Features</label>
              </div>
              <div class="col-12 col-md-4 form-floating">
                <input type="text" name="condition" v-model="form.condition" class="form-control" placeholder="Animal condition" aria-label="Animal condition" id="floating_animal_condition_found">
                <label for="floating_animal_condition_found" class="form-label fw-bold">Condition</label>
              </div>
              <div class="col-12 col-md-4 form-floating">
                <input type="text" name="temporary_shelter" v-model="form.temporary_shelter" class="form-control" placeholder="Animal temporary" aria-label="Animal temporary shelter" id="floating_animal_temporary_shelter_found">
                <label for="floating_animal_temporary_shelter_found" class="form-label fw-bold">Temporary Shelter</label>
              </div>
            </div>

            <div class="row g-2 mt-2">
              <div class="col-12 col-md-8 form-floating">
                <input type="text" name="found_location" v-model="form.found_location" class="form-control" placeholder="Animal found location" aria-label="Animal found location" id="floating_animal_found_location" required>
                <label for="floating_animal_found_location" class="form-label fw-bold">Found Location</label>
              </div>
              <div class="col-12 col-md-4 form-floating">
                <input type="date" name="found_date" v-model="form.found_date" class="form-control" placeholder="Animal last seen date" aria-label="Animal found date" id="floating_animal_found_date" required>
                <label for="floating_animal_found_date" class="form-label fw-bold">Found Date</label>
              </div>
            </div>

            <div class="row g-2 mt-2">
              <div class="col-12 col-md-8">
                <label for="image_found" class="form-label fw-bold">Update Image</label>
                <input type="file" name="image" id="image_found" class="form-control" accept="image/*" @change="handleImageChange">
                <small class="text-muted mt-3">Leave blank to keep existing image</small>
                <div class="mb-2 mt-2">
                  <img id="reportImageFound" class="w-100 h-100 object-fit-cover rounded-4" style="max-height: 150px;">
                </div>
              </div>
              <div class="col-12 col-md-4">
                <label for="report_status_found" class="form-label fw-bold">Update Report Status</label>
                <select name="status" v-model="form.status" id="report_status_found" class="form-select" aria-label="size-select" required>
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
  const reportAnimalSpecies = ref(null)
  const reportAnimalBreed = ref(null)
  const reportAnimalColor = ref(null)
  const reportAnimalSex = ref(null)
  const reportAnimalAgeEstimate = ref(null)
  const reportAnimalSize = ref(null)
  const reportAnimalDistinctiveFeatures = ref(null)
  const reportAnimalFoundLocation = ref(null)
  const reportAnimalFoundDate = ref(null)
  const reportAnimalStatus = ref(null)
  const reportAnimalCondition = ref(null)
  const reportAnimalTemporaryShelter = ref(null)

  const form = useForm({
    species: '',
    breed: '',
    color:'',
    sex: '',
    age_estimate:'',
    size:'',
    distinctive_features:'',
    found_location: '',
    found_date:'',
    status:'',
    condition:'',
    temporary_shelter:'',
    image:null,
    type:'found',
    user_id:'',
    _method:'PUT'
  })

  function initializeForm(){
    form.species = reportAnimalSpecies || ''
    form.breed = reportAnimalBreed || ''
    form.color = reportAnimalColor || ''
    form.sex = reportAnimalSex || ''
    form.age_estimate = reportAnimalAgeEstimate || ''
    form.size = reportAnimalSize || ''
    form.distinctive_features = reportAnimalDistinctiveFeatures || ''
    form.found_location = reportAnimalFoundLocation || ''
    form.found_date = reportAnimalFoundDate ||''
    form.status = reportAnimalStatus || ''
    form.condition = reportAnimalCondition || ''
    form.temporary_shelter = reportAnimalTemporaryShelter || ''
    form.user_id = props.user?.id || ''
    form.image = null
  }

  onMounted(() => {
    const modalEl = document.getElementById('updateFoundReportModal')
    
    modalEl.addEventListener('show.bs.modal', (event) => {
      const button = event.relatedTarget  
      
      reportId.value = button.getAttribute('data-report-id')


      reportAnimalSpecies.value = button.getAttribute('data-report-species')

      reportAnimalBreed.value = button.getAttribute('data-report-breed')

      reportAnimalColor.value = button.getAttribute('data-report-color')

      reportAnimalSex.value = button.getAttribute('data-report-sex')

      reportAnimalAgeEstimate.value = button.getAttribute('data-report-age-estimate')

      reportAnimalSize.value = button.getAttribute('data-report-size')

      reportAnimalDistinctiveFeatures.value = button.getAttribute('data-report-distinctive-features')

      reportAnimalFoundLocation.value = button.getAttribute('data-report-location')

      reportAnimalFoundDate.value = button.getAttribute('data-report-found-date')

      reportAnimalStatus.value = button.getAttribute('data-report-status')

      reportAnimalCondition.value = button.getAttribute('data-report-condition')

      reportAnimalTemporaryShelter.value = button.getAttribute('data-report-temporary-shelter')

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
    formData.append('species', form.species || '')
    formData.append('breed', form.breed || '')
    formData.append('color', form.color || '')
    formData.append('sex', form.sex|| '')
    formData.append('age_estimate', form.age_estimate || '')
    formData.append('size', form.size || '')
    formData.append('distinctive_features', form.distinctive_features || '')
    formData.append('found_location', form.found_location|| '')
    formData.append('found_date', form.found_date || '')
    formData.append('status', form.status || '')
    formData.append('type','found')
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
    const modalEl = document.getElementById('updateFoundReportModal')
    const modal = Modal.getInstance(modalEl)
    if (modal) {
      modal.hide()
    }

    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove())
    document.body.classList.remove('modal-open')
    document.body.style.removeProperty('overflow')
    document.body.style.removeProperty('padding-right')
    
    reportId.value = null
    reportAnimalSpecies.value = null
    reportAnimalBreed.value = null
    reportAnimalColor.value = null
    reportAnimalSex.value = null
    reportAnimalAgeEstimate.value = null
    reportAnimalSize.value = null
    reportAnimalDistinctiveFeatures.value = null
    reportAnimalFoundLocation.value = null
    reportAnimalFoundDate.value = null
    reportAnimalStatus.value = null
    reportAnimalCondition.value = null
    reportAnimalTemporaryShelter.value = null

    form.reset()
  }
  
</script>