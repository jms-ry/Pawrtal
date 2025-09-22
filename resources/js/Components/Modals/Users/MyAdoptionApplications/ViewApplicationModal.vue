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
            <hr class="text-dark mt-3 mb-2">
            <h6 class="fw-bolder text-uppercase font-monospace">Inspection Details:</h6>
            <div class="d-flex flex-column align-items-start ms-2">
              <span class="mt-2 ms-2 me-4">Inspection Date:  </span>
              <span class="mt-2 ms-2 me-4">Inspection Location:  </span>
              <span class="mt-2 ms-2 me-4">Inspection Officer:  </span>
            </div>
            <hr class="text-dark mt-3 mb-2">
            <h6 class="fw-bolder text-uppercase font-monospace">Review Details:</h6>
            <div class="d-flex flex-column align-items-start ms-2">
              <span class="mt-2 ms-2 me-4">Review Notes:  </span>
              <span class="mt-2 ms-2 me-4">Reviewed By:  </span>
            </div>
          </div>
        </div>
        <div class="modal-footer border-0 bg-info-subtle">
          <div v-if="applicationStatus === 'pending'" class="align-self-start">
            <button type="button" class="btn btn-warning" :data-application-id="applicationId" data-bs-toggle="modal" data-bs-target="#cancelApplicationModal">Cancel Application</button>
          </div>
          <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <CancelApplicationModal />
</template>

<script setup>
  import { ref, onMounted} from 'vue'
  import CancelApplicationModal from './CancelApplicationModal.vue'



  const applicationId = ref(null)
  const applicationRescueName = ref(null)
  const applicationStatus = ref(null)
  const applicationStatusLabel = ref(null)
  const applicationDate = ref(null)
  const inspectionStartDate = ref(null)
  const inspectionEndDate = ref(null)
  const reasonForAdoption = ref(null)

  onMounted(() => {
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

      if(applicationStatus.value === 'pending'){
        statusLabelBadge.classList.add('text-bg-info')
        statusLabelBadge.innerHTML = `<i class="bi bi-hourglass-split me-1"></i> ${applicationStatusLabel.value}`
      }else if(applicationStatus.value === 'approved'){
        statusLabelBadge.classList.add('text-bg-success')
        statusLabelBadge.innerHTML = `<i class="bi bi-check-circle me-1"></i> ${applicationStatusLabel.value}`
      }else if(applicationStatus.value === 'rejected'){
        statusLabelBadge.classList.add('text-bg-danger')
        statusLabelBadge.innerHTML = `<i class="bi bi-x-circle me-1"></i> ${applicationStatusLabel.value}`
      }else if (applicationStatus.value === 'under review'){
        statusLabelBadge.classList.add('text-bg-primary')
        statusLabelBadge.innerHTML = `<i class="bi bi-search me-1"></i> ${applicationStatusLabel.value}`
      }else if(applicationStatus.value === 'archived'){
        statusLabelBadge.classList.add('text-bg-light')
        statusLabelBadge.innerHTML = `<i class="bi bi-archive-fill me-1"></i> ${applicationStatusLabel.value}`
      }else{
        statusLabelBadge.classList.add('text-bg-warning')
        statusLabelBadge.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-1"></i> ${applicationStatusLabel.value}`
      }
    });
  });
</script>