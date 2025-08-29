<div class="modal fade me-2" id="profileReminderModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="profileReminderModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-sm-scrollable">
    <div class="modal-content ">
      <div class="modal-body bg-info-subtle border-0">
        <div class="d-flex d-flex-row justify-content-start align-items-center display-5 p-1 mt-3">
          <i class="bi bi-exclamation-diamond-fill me-2 text-warning fs-2"></i>
          <h4 class="fw-bold font-monospace mt-2 fs-5">You need  to comply the following:</h4>
        </div>
        <div class="d-flex d-flex-row justify-content-start align-items-center display-5 p-1 mt-2 ms-4">
          @if ($user?->address)
            <i class="bi bi-check2-circle me-2 text-primary fw-bold fs-2"></i>
            <h4 class="fw-bold font-monospace mt-2 fs-6">Address information is provided.</h4>
          @else
            <i class="bi bi-x-circle me-2 text-danger fw-bold fs-2"></i>
            <h4 class="fw-bold font-monospace mt-2 fs-6">Please add your address.</h4>
          @endif
        </div>
        <div class="d-flex d-flex-row justify-content-start align-items-center display-5 p-1 mt-2 ms-4">
          @if ($user?->household)
            <i class="bi bi-check2-circle me-2 text-primary fw-bold fs-2"></i>
            <h4 class="fw-bold font-monospace mt-2 fs-6">Household information is provided.</h4>
          @else
            <i class="bi bi-x-circle me-2 text-danger fw-bold fs-2"></i>
            <h4 class="fw-bold font-monospace mt-2 fs-6">Please add your household details.</h4>
          @endif
        </div>
        <div class="d-flex d-flex-row justify-content-end align-items-center mb-1 mt-3">
          <a href="" class="btn btn-primary me-1" id="updateProfileBtn">Update Profile</a>
          <button class="btn btn-danger" type="button"  data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</div>