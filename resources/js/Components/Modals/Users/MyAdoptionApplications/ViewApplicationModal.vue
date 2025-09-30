<template>
  <div class="modal fade me-2" id="viewApplicationModal" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="viewApplicationModalLabel">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-body border-0 bg-info-subtle">
          <div class="d-flex justify-content-between">
            <h3 class="fs-5 fw-bolder mt-2 text-uppercase font-monospace">{{applicationRescueName}}'s Adoption Application</h3>
            <span class="badge d-flex justify-content-center align-items-center text-dark fw-bold" id="applicationStatusLabel"> Status</span>
          </div>
          <div class="card border-0 bg-info-subtle">
            <hr class="text-dark mt-3 mb-2">
            <h6 class="fw-bolder text-uppercase font-monospace">Adoption Details:</h6>
            <div class="d-flex flex-column align-items-start ms-2">
              <span class="mt-2 ms-2 me-4 ">Application Date: <strong class="ms-1">{{ applicationDate }}</strong> </span>
              <span class="mt-2 ms-2 me-4">Inspection Start Date:  <strong class="ms-1">{{ inspectionStartDate }}</strong></span>
              <span class="mt-2 ms-2 me-4">Inspection End Date: <strong class="ms-1">{{ inspectionEndDate }}</strong> </span>
              <span class="mt-2 ms-2 me-4">Reason for Adoption: </span>
              <textarea readonly class="form-control mt-2 fw-bolder">{{ reasonForAdoption }}</textarea>
            </div>
            <div v-show="user?.role !== 'regular_user' && inspectionScheduleCount === 0 && applicationStatus !=='cancelled'" >
              <hr class="text-dark mt-3 mb-2">
              <h6 class="fw-bolder text-uppercase font-monospace">Applicant Address Details:</h6>
              <div class="d-flex flex-column align-items-start ms-2">
                <span class="mt-2 ms-2 me-4">Full Address: <strong class="ms-1">{{ fullAddress }}</strong> </span>
              </div>
              <hr class="text-dark mt-3 mb-2">
              <h6 class="fw-bolder text-uppercase font-monospace mt-1">Applicant Household Details:</h6>
              <div class="d-flex flex-column align-items-start ms-2">
                <span class="mt-2 ms-2 me-4">House Structure: <strong class="ms-1">{{ houseStructure }}</strong> </span>
                <span class="mt-2 ms-2 me-4">Household members: <strong class="ms-1">{{ houseMembers }}</strong> </span>
                <span class="mt-2 ms-2 me-4">Number of children in the house: <strong class="ms-1">{{ numberOfChildren }}</strong> </span>
                <span class="mt-2 ms-2 me-4">Current Pets: <strong class="ms-1">{{ currentPets }}</strong> </span>
                <span class="mt-2 ms-2 me-4">Number of Current Pets: <strong class="ms-1">{{ numberOfCurrentPets }}</strong> </span>
              </div>
              <hr class="text-dark mt-3 mb-2">
              <h6 class="fw-bolder text-uppercase font-monospace mt-1">Verification Documents:</h6>
              <div class="d-flex flex-column align-items-start ms-2">
                <span class="mt-2 ms-2 me-4">
                  Valid ID: 
                  <a :href="validIdUrl" target="_blank" class="fw-bold font-monospace text-decoration-underline ms-1" :class="{ 'text-dark': !visitedLinks.validId, 'text-danger': visitedLinks.validId }" @click="markVisited('validId')">
                    <i class="bi bi-file-image me-1"></i>View
                  </a>
                </span>
              </div>
              <div v-if="supportingDocuments && supportingDocuments.length > 0" class="mt-2 ms-2 me-4 w-100">
                <span class="mt-2 ms-2 me-4 d-block mb-2">Supporting Documents:</span>
                <div class="d-flex flex-column ms-4">
                  <span v-for="(docUrl, index) in supportingDocuments" :key="index" class="mb-2">
                    Document {{ index + 1 }}: 
                    <a :href="docUrl" target="_blank" class="fw-bold font-monospace text-decoration-underline ms-1" :class="{ 'text-dark': !visitedLinks.supporting[index], 'text-danger': visitedLinks.supporting[index] }" @click="markVisited('supporting', index)">
                      <i class="bi bi-file-earmark-text me-1"></i>View
                    </a>
                  </span>
                </div>
              </div>
              <span v-else class="mt-2 ms-2 me-4 text-muted fst-italic">
                No supporting documents uploaded
              </span>
            </div>
            <div>
              <hr class="text-dark mt-3 mb-2">
              <h6 class="fw-bolder text-uppercase font-monospace mt-1">Application Progress:</h6>
              <div class="d-flex align-items-center justify-content-between mt-4 px-3">
                <div class="d-flex flex-column align-items-center">
                  <div class="rounded-circle d-flex justify-content-center align-items-center mb-2" :class="applicationStatus === 'pending' || applicationStatus === 'under_review' || applicationStatus === 'approved' || applicationStatus === 'rejected' || applicationStatus === 'cancelled' ? 'bg-info text-white' : 'bg-secondary text-white'"style="width: 50px; height: 50px;">
                    <i class="bi bi-hourglass-split fs-5"></i>
                  </div>
                  <strong class="text-center small">Pending</strong>
                  <div class="text-muted small text-center">Submitted</div>
                </div>
                <div class="flex-grow-1 border-top border-2 mx-2" :class="applicationStatus === 'under_review' || applicationStatus === 'approved' || applicationStatus === 'rejected' || applicationStatus === 'cancelled' ? 'border-info' : 'border-secondary'" style="height: 2px; margin-bottom: 60px;"></div>
                <div class="d-flex flex-column align-items-center">
                  <div class="rounded-circle d-flex justify-content-center align-items-center mb-2" :class="applicationStatus === 'under_review' || applicationStatus === 'approved' || applicationStatus === 'rejected' ? 'bg-primary text-white' : applicationStatus === 'cancelled' ? 'bg-warning text-dark' : 'bg-secondary text-white'" style="width: 50px; height: 50px;">
                    <i :class="applicationStatus === 'cancelled' ? 'bi bi-exclamation-triangle-fill fs-5' : 'bi bi-search fs-5'"></i>
                  </div>
                  <strong class="text-center small" v-if="applicationStatus === 'cancelled'">Cancelled</strong>
                  <strong class="text-center small" v-else>Under Review</strong>
                  <div class="text-muted small text-center" v-if="applicationStatus === 'cancelled'">Cancelled</div>
                  <div class="text-muted small text-center" v-else>Reviewing</div>
                </div>
                <div v-if="applicationStatus !== 'cancelled'" class="flex-grow-1 border-top border-2 mx-2" :class="applicationStatus === 'approved' || applicationStatus === 'rejected' ? 'border-primary' : 'border-secondary'" style="height: 2px; margin-bottom: 60px;"></div>
                <div v-if="applicationStatus !== 'cancelled'" class="d-flex flex-column align-items-center">
                  <div class="rounded-circle d-flex justify-content-center align-items-center mb-2" :class="applicationStatus === 'approved' ? 'bg-success text-white' : applicationStatus === 'rejected' ? 'bg-danger text-white' : 'bg-secondary text-white'" style="width: 50px; height: 50px;">
                    <i :class="applicationStatus === 'approved' ? 'bi bi-check-circle fs-5' : applicationStatus === 'rejected' ? 'bi bi-x-circle fs-5' : 'bi bi-clock fs-5'"></i>
                  </div>
                  <strong class="text-center small" v-if="applicationStatus === 'approved'">Approved</strong>
                  <strong class="text-center small" v-else-if="applicationStatus === 'rejected'">Rejected</strong>
                  <strong class="text-center small" v-else>Decision</strong>
                  <div class="text-muted small text-center" v-if="applicationStatus === 'approved'">Approved</div>
                  <div class="text-muted small text-center" v-else-if="applicationStatus === 'rejected'">Rejected</div>
                  <div class="text-muted small text-center" v-else>Pending</div>
                </div>
              </div>
            </div>
            <div v-show="inspectionScheduleCount > 0 && applicationStatus === 'under_review'" >
              <hr class="text-dark mt-3 mb-2">
              <h6 class="fw-bolder text-uppercase font-monospace mt-1">Inspection Details:</h6>
              <div class="d-flex flex-column align-items-start ms-2">
                <span class="mt-2 ms-2 me-4">Inspection Date: <strong class="ms-1">{{ inspectionDate }}</strong> </span>
                <span class="mt-2 ms-2 me-4">Inspection Location: <strong class="ms-1">{{ inspectionLocation }}</strong> </span>
                <span class="mt-2 ms-2 me-4">Inspection Officer: <strong class="ms-1">{{ inspectorName }}</strong> </span>
              </div>
            </div>
            <div v-show="reviewNotes">
              <hr class="text-dark mt-3 mb-2">
              <h6 class="fw-bolder text-uppercase font-monospace mt-1">Review Details:</h6>
              <div class="d-flex flex-column align-items-start ms-2">
                <span class="mt-2 ms-2 me-4">Reviewed By: <strong class="ms-1">{{ reviewedBy }}</strong> </span>
                <span class="mt-2 ms-2 me-4">Review Date: <strong class="ms-1">{{ reviewedDate }}</strong> </span>
                <span class="mt-2 ms-2 me-4">Review Notes: <strong class="ms-1"></strong> </span>
                <textarea readonly class="form-control mt-2 fw-bolder">{{ reviewNotes }}</textarea>
              </div>
            </div>
          </div>
          <hr class="text-dark mt-3 mb-2">
        </div>
        <div class="modal-footer border-0 bg-info-subtle">
          <div v-if="isAdminStaff === 'false' && applicationStatus === 'pending'" class="align-self-start">
            <button type="button" class="btn btn-warning" :data-application-id="applicationId" data-bs-toggle="modal" data-bs-target="#cancelApplicationModal">Cancel Application</button>
          </div>
          <div v-else-if="isAdminStaff === 'true' && applicationStatus === 'pending'" class="align-self-start">
            <span data-bs-toggle="tooltip" data-bs-placement="top" title="Make sure to verify all the documents.">
              <button type="button" class="btn btn-info" disabled data-bs-toggle="modal" data-bs-target="#setInspectionScheduleModal" 
                :data-application-id="applicationId"
                :data-application-start-date="inspectionStartDate"
                :data-application-end-date="inspectionEndDate" 
                :data-application-address="fullAddress"
                >Set Inspection Schedule
              </button>
            </span>
          </div>
          <div v-else-if="isAdminStaff === 'true' && applicationStatus === 'under_review'" class="align-self-start">
            <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#makeDecisionModal" 
                :data-application-id="applicationId"
                >Make Decision
              </button>
          </div>
          <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-danger" @click="closeModal">Close</button>
          </div>
          <div v-if="isAdminStaff === 'true' && applicationStatus === 'pending'" class="d-block d-md-none" id="reminderSmall">
            <small class="text-muted d-block mt-1 fst-italic">
              Button disabled. Make sure to verify all the documents first.
            </small>
          </div>
        </div>
      </div>
    </div>
  </div>
  <CancelApplicationModal />
  <SetInspectionSchedule
    :inspectors="inspectors"
  />
  <MakeDecisionModal
    :user="user"
  />
</template>

<script setup>
  import { ref, computed, onMounted, nextTick } from 'vue'
  import CancelApplicationModal from './CancelApplicationModal.vue'
  import SetInspectionSchedule from '../../SetInspectionSchedule.vue'
  import { Modal, Tooltip } from 'bootstrap'
  import MakeDecisionModal from '../../MakeDecisionModal.vue'

  const props = defineProps({
    user: {
      type: Object,
    },
    inspectors: Object
  })
  const visitedLinks = ref({
    validId: false,
    supporting: []
  })

  function markVisited(type, index = null) {
    if (type === 'validId') {
      visitedLinks.value.validId = true
    } else if (type === 'supporting') {
      visitedLinks.value.supporting[index] = true
    }
    checkIfAllVisited()
  }

  const allDocsVisited = computed(() => {
    if (!validIdUrl.value) return false
    const validIdVisited = visitedLinks.value.validId
    const supportingVisited = supportingDocuments.value.length === 0 || 
      visitedLinks.value.supporting.every(v => v)
    return validIdVisited && supportingVisited
  })

  function checkIfAllVisited() {
    if (allDocsVisited.value) {
      nextTick(() => {
        const btn = document.querySelector('[data-bs-target="#setInspectionScheduleModal"]')
        if (btn) {
          btn.removeAttribute('disabled')
          const tooltip = Tooltip.getInstance(btn)
          if (tooltip) {
            tooltip.dispose()
          }
          const warning = document.querySelector('#reminderSmall')
          warning.classList.add('d-none')
        }
      })
    }
  }
  const applicationId = ref(null)
  const applicationRescueName = ref(null)
  const applicationStatus = ref(null)
  const applicationStatusLabel = ref(null)
  const applicationDate = ref(null)
  const inspectionStartDate = ref(null)
  const inspectionEndDate = ref(null)
  const reasonForAdoption = ref(null)
  const fullAddress = ref(null)
  const houseStructure = ref(null)
  const houseMembers = ref(null)
  const numberOfChildren = ref(null)
  const currentPets = ref(null)
  const numberOfCurrentPets = ref(null)
  const isAdminStaff = ref(null)
  const inspectionScheduleCount = ref(null)
  const inspectionLocation = ref(null)
  const inspectorName = ref(null)
  const inspectionDate = ref(null)
  const validIdUrl = ref(null)
  const supportingDocuments = ref([])
  const reviewNotes = ref(null)
  const reviewedDate = ref(null)
  const reviewedBy = ref(null)
  onMounted(() => {

    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
      new Tooltip(el)
    })
    const viewApplicationModal = document.getElementById('viewApplicationModal');
    viewApplicationModal.addEventListener('show.bs.modal', (event) => {
      const button = event.relatedTarget;
      applicationId.value = button.getAttribute('data-application-id');
      applicationRescueName.value = button.getAttribute('data-application-rescue-name');
      applicationStatus.value = button.getAttribute('data-application-status');
      applicationStatusLabel.value = button.getAttribute('data-application-status-label');
      applicationDate.value = button.getAttribute('data-application-application-date');
      inspectionStartDate.value = button.getAttribute('data-application-start-date');
      inspectionEndDate.value = button.getAttribute('data-application-end-date');
      reasonForAdoption.value = button.getAttribute('data-application-reason-for-adoption');

      const statusLabelBadge = document.getElementById('applicationStatusLabel');
      statusLabelBadge.classList.remove(
        'text-bg-info',
        'text-bg-success',
        'text-bg-danger',
        'text-bg-primary',
        'text-bg-warning'
      )
      if(applicationStatus.value === 'pending'){
        statusLabelBadge.classList.add('text-bg-info')
        statusLabelBadge.innerHTML = `<i class="bi bi-hourglass-split me-1"></i> ${applicationStatusLabel.value}`
      }else if(applicationStatus.value === 'approved'){
        statusLabelBadge.classList.add('text-bg-success')
        statusLabelBadge.innerHTML = `<i class="bi bi-check-circle me-1"></i> ${applicationStatusLabel.value}`
      }else if(applicationStatus.value === 'rejected'){
        statusLabelBadge.classList.add('text-bg-danger')
        statusLabelBadge.innerHTML = `<i class="bi bi-x-circle me-1"></i> ${applicationStatusLabel.value}`
      }else if (applicationStatus.value === 'under_review'){
        statusLabelBadge.classList.add('text-bg-primary')
        statusLabelBadge.innerHTML = `<i class="bi bi-search me-1"></i> ${applicationStatusLabel.value}`
      }else{
        statusLabelBadge.classList.add('text-bg-warning')
        statusLabelBadge.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-1"></i> ${applicationStatusLabel.value}`
      }

      fullAddress.value = button.getAttribute('data-applicant-full-address');
      houseStructure.value = button.getAttribute('data-applicant-housestucture');
      houseMembers.value = button.getAttribute('data-applicant-household-members');
      numberOfChildren.value = button.getAttribute('data-applicant-number-of-children');
      currentPets.value = button.getAttribute('data-applicant-current-pets');
      numberOfCurrentPets.value = button.getAttribute('data-applicant-number-of-current-pets');

      isAdminStaff.value = button.getAttribute('data-application-logged-user-is-admin-or-staff');

      inspectionScheduleCount.value = Number(button.getAttribute('data-applicaiton-inspection-schedule-count'))
      inspectionLocation.value = button.getAttribute('data-application-inspection-location')
      inspectorName.value = button.getAttribute('data-application-inspector-name')
      inspectionDate.value = button.getAttribute('data-application-inspection-date')

      validIdUrl.value = button.getAttribute('data-application-valid-id-url')
      const supportingDocsJson = button.getAttribute('data-application-supporting-documents');
      supportingDocuments.value = supportingDocsJson ? JSON.parse(supportingDocsJson) : [];

      visitedLinks.value.validId = false
      visitedLinks.value.supporting = supportingDocuments.value.map(() => false)

      reviewNotes.value = button.getAttribute('data-application-review-notes')
      reviewedDate.value = button.getAttribute('data-application-review-date')
      reviewedBy.value = button.getAttribute('data-application-reviewer')
    });
  });

  function closeModal(){
    const modalEl = document.getElementById('viewApplicationModal')
    const modal = Modal.getInstance(modalEl)
    if (modal) {
      modal.hide()
    }

    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove())
    document.body.classList.remove('modal-open')
    document.body.style.removeProperty('overflow')
    document.body.style.removeProperty('padding-right')
    
    applicationId.value = null
    applicationRescueName.value = null
    applicationStatus.value =  null
    applicationStatusLabel.value =  null
    applicationDate.value =  null
    inspectionStartDate.value =  null
    inspectionEndDate.value =  null
    reasonForAdoption.value =  null
    fullAddress.value =  null
    houseStructure.value =  null
    houseMembers.value =  null
    numberOfChildren.value =  null
    currentPets.value =  null
    numberOfCurrentPets.value =  null
    isAdminStaff.value =  null
    inspectionScheduleCount.value =  null
    inspectionLocation.value =  null
    inspectorName.value =  null
    inspectionDate.value =  null
    validIdUrl.value = null 
    supportingDocuments.value = [] 
  }
</script>