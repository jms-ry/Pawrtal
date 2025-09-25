<template>
  <div class="modal fade me-2 border-rounded" id="restoreRescueProfileModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="restoreRescueProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-sm-scrollable">
      <div class="modal-content ">
        <form @submit.prevent="submitForm" >
          <div class="modal-body bg-info-subtle border-0">
            <div class="d-flex d-flex-row justify-content-start align-items-center mt-4 display-5 p-1">
              <i class="bi bi-archive-fill me-2 text-warning fs-2"></i>
              <h4 class="fw-bold font-monospace mt-2 ms-1">Restore {{ rescue.name_formatted }}'s Profile?</h4>
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

  const props = defineProps({
    rescue: {
    type: Object,
    required: true,
    },
  })

  function submitForm() {
    router.patch(`/rescues/${props.rescue.id}/restore`, {}, {
      preserveScroll: false,
      preserveState: false,
      onSuccess: () => {
        const modalEl = document.getElementById('restoreRescueProfileModal')
        const modal = Modal.getInstance(modalEl)
        if (modal) {
          modal.hide()
        }

        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove())
        document.body.classList.remove('modal-open')
        document.body.style.removeProperty('overflow')
        document.body.style.removeProperty('padding-right')
      }
    })
  }
</script>