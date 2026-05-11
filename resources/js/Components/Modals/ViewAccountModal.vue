<template>
  <div class="modal fade me-2" id="viewUserModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="viewUserModalLabel">
   <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-body border-0 bg-info-subtle">
          <h6 class="fw-bolder mt-2 text-uppercase font-monospace mt-1"><strong >Account Details: </strong></h6>
          <hr class="text-dark mt-3 mb-2">
          <div class="d-flex flex-column align-items-start ms-2">
            <strong class="mt-2 ms-2 me-4">First Name:  <span class="fw-lighter">{{ accountFirstName }}</span></strong>
            <strong class="mt-2 ms-2 me-4">Last Name:  <span class="fw-lighter">{{ accountLastName }}</span></strong>
            <strong class="mt-2 ms-2 me-4">Email:  <span class="fw-lighter">{{ accountEmail }}</span></strong>
            <strong class="mt-2 ms-2 me-4">Contact Number:  <span class="fw-lighter">{{ accountContactNumber }}</span></strong>
          </div>
          <hr class="text-dark mt-3 mb-2">
        </div>
        <div class="modal-footer border-0 bg-info-subtle">
          <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { onMounted,ref } from 'vue';

  const accountFirstName = ref('')
  const accountLastName = ref('')
  const accountEmail = ref('')
  const accountContactNumber = ref('')
  onMounted(() => {
    const modalEl = document.getElementById('viewUserModal')

    modalEl.addEventListener('show.bs.modal', (event) => { 
      const button = event.relatedTarget

      accountFirstName.value = button.getAttribute('data-user-first-name')
      accountLastName.value = button.getAttribute('data-user-last-name')
      accountEmail.value = button.getAttribute('data-user-email')
      accountContactNumber.value = button.getAttribute('data-user-contact-number')
    })

  })
</script>