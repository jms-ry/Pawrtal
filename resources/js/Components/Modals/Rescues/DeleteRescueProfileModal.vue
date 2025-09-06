<template>
  <div class="modal fade me-2 border-rounded" id="deleteRescueProfileModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteRescueProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-sm-scrollable">
      <div class="modal-content ">
        <form @submit.prevent="submitForm" >
          <div class="modal-body bg-info-subtle border-0">
            <div class="d-flex d-flex-row justify-content-start align-items-center mt-4 display-5 p-1">
              <i class="bi bi-trash3-fill me-2 text-danger fs-2"></i>
              <h4 class="fw-bold font-monospace mt-2">Delete {{ rescue.name_formatted }}'s Profile?</h4>
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
  const props = defineProps({
    rescue:{
      type: Object,
      required: true
    }
  });

  function submitForm() {
    Inertia.delete(`/rescues/${props.rescue.id}`)
  }
</script>