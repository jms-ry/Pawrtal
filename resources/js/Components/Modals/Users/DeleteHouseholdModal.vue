<template>
  <div class="modal fade me-2" id="deleteHouseholdModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteHouseholdModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-sm-scrollable">
    <div class="modal-content ">
      <form @submit.prevent="submitForm">
        <div class="modal-body bg-info-subtle border-0">
          <div class="d-flex d-flex-row justify-content-start align-items-center mt-4">
            <i class="bi bi-trash3-fill me-3 text-danger fs-2"></i>
            <p class="fw-bold font-monospace mt-2 fs-5 text-start">Are you sure you wanna delete your Household Information?</p>
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

  const householdId = ref(null)

  onMounted(() => {
    const modalEl = document.getElementById('deleteHouseholdModal')
    
    modalEl.addEventListener('show.bs.modal', (event) => {
      const button = event.relatedTarget 
      
      householdId.value = button.getAttribute('data-household-id')
    })
  })

  function submitForm() {
    if (!householdId.value) {
      console.error('No household ID available for deletion')
      return
    }
    
    router.delete(`/households/${householdId.value}`, {
      preserveScroll: false,
      preserveState: false,
      onSuccess: () => {
        closeModal()
      },
      onError: (errors) => {
        console.error('Error deleting household:', errors)
      }
    })
  }

  function closeModal(){
    const modalEl = document.getElementById('deleteHouseholdModal')
    const modal = Modal.getInstance(modalEl)
    if (modal) {
      modal.hide()
    }

    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove())
    document.body.classList.remove('modal-open')
    document.body.style.removeProperty('overflow')
    document.body.style.removeProperty('padding-right')
    
    householdId.value = null
  }
</script>