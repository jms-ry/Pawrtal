<template>
  <div class="modal fade me-2" id="unarchiveReportModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="unarchiveReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-sm-scrollable">
      <div class="modal-content ">
        <form @submit.prevent="submitForm" class="">
          <div class="modal-body bg-info-subtle border-0">
            <div class="d-flex d-flex-row justify-content-start align-items-center mt-4 display-5 p-1">
              <i class="bi bi-archive-fill me-2 text-success fs-2"></i>
              <h4 class="fw-bold font-monospace mt-2">Unarchive <span id="unarchiveTitle"></span> Animal Report?</h4>
            </div>
            <div class="d-flex d-flex-row justify-content-end align-items-center mb-1 mt-3">
              <button class="btn btn-success me-1" type="submit">Yes</button>
              <button class="btn btn-warning" type="button"  data-bs-dismiss="modal">Cancel</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
<script setup>
  import { Modal } from 'bootstrap'
  import { router } from '@inertiajs/vue3'
  import { ref, onMounted } from 'vue'

  const reportId = ref(null)

  onMounted(() => {
    const modalEl = document.getElementById('unarchiveReportModal')
    
    modalEl.addEventListener('show.bs.modal', (event) => {
      const button = event.relatedTarget 
      
      reportId.value = button.getAttribute('data-report-id')
    })
  })

  function submitForm() {
    
    router.patch(`/reports/${reportId.value}/restore`, {}, {
      preserveScroll: false,
      preserveState: false,
      onSuccess: () => {
        closeModal()
      },
      onError: (errors) => {
        console.error('Error deleting report:', errors)
      }
    })
  }

  function closeModal(){
    const modalEl = document.getElementById('unarchiveReportModal')
    const modal = Modal.getInstance(modalEl)
    if (modal) {
      modal.hide()
    }

    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove())
    document.body.classList.remove('modal-open')
    document.body.style.removeProperty('overflow')
    document.body.style.removeProperty('padding-right')
    
    reportId.value = null
  }
</script>