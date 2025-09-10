<template>
  <div class="modal fade me-2" id="deleteReportModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-sm-scrollable">
      <div class="modal-content ">
        <form @submit.prevent="submitForm" class="">
          <div class="modal-body bg-info-subtle border-0">
            <div class="d-flex d-flex-row justify-content-start align-items-center mt-4 display-5 p-1">
              <i class="bi bi-trash3-fill me-2 text-danger fs-2"></i>
              <h4 class="fw-bold font-monospace mt-2">Delete <span id="title"></span> Animal Report?</h4>
            </div>
            <div class="d-flex d-flex-row justify-content-end align-items-center mb-1 mt-3">
              <button class="btn btn-danger me-1" type="submit">Yes</button>
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
    const modalEl = document.getElementById('deleteReportModal')
    
    modalEl.addEventListener('show.bs.modal', (event) => {
      const button = event.relatedTarget 
      
      reportId.value = button.getAttribute('data-report-id')
    })
  })

  function submitForm() {
    if (!reportId.value) {
      console.error('No report ID available for deletion')
      return
    }
    
    router.delete(`/reports/${reportId.value}`, {
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
    const modalEl = document.getElementById('deleteReportModal')
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