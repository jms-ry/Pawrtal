<template>
  <div class="modal fade me-2" id="restoreApplicationModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="restoreApplicationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-sm-scrollable">
    <div class="modal-content ">
      <form @submit.prevent="submitForm">
        <div class="modal-body bg-info-subtle border-0">
          <div class="d-flex d-flex-row justify-content-start align-items-center mt-4">
            <i class="bi bi-archive-fill me-3 text-success fs-2"></i>
            <p class="fw-bold font-monospace mt-2 fs-5 text-start">Wanna Unarchive this application?</p>
          </div>
          <div class="d-flex d-flex-row justify-content-end align-items-center mb-1 mt-3">
            <button class="btn btn-warning me-1" type="submit">Yes</button>
            <button class="btn btn-danger" type="button"  data-bs-dismiss="modal">Cancel</button>
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

  const applicationId = ref(null)

  onMounted(() => {
    const modalEl = document.getElementById('restoreApplicationModal')
    
    modalEl.addEventListener('show.bs.modal', (event) => {
      const button = event.relatedTarget 
      
      applicationId.value = button.getAttribute('data-application-id')
    })
  })

  function submitForm() {
    if (!applicationId.value) {
      console.error('No ID available')
      return
    }
    
    router.patch(`/adoption-applications/${applicationId.value}/restore`, {}, {
      preserveScroll: false,
      preserveState: false,
      onSuccess: () => {
        closeModal()
      },
    })
  }

  function closeModal(){
    const modalEl = document.getElementById('restoreApplicationModal')
    const modal = Modal.getInstance(modalEl)
    if (modal) {
      modal.hide()
    }

    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove())
    document.body.classList.remove('modal-open')
    document.body.style.removeProperty('overflow')
    document.body.style.removeProperty('padding-right')
    
    applicationId.value = null
  }
</script>