<div class="modal fade me-2" id="loginReminderModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="loginReminderModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-sm-scrollable">
    <div class="modal-content ">
      <div class="modal-body bg-info-subtle border-0">
        <div class="d-flex d-flex-row justify-content-start align-items-center mt-4 display-5 p-1">
          <i class="bi bi-exclamation-diamond-fill me-2 text-warning fs-2"></i>
          <h4 class="fw-bold font-monospace mt-2 fs-5">You need  to have an account first!</h4>
        </div>
        <div class="d-flex d-flex-row justify-content-end align-items-center mb-1 mt-3">
          <a href="{{ url('/register') }}" class="btn btn-primary me-1">Create account!</a>
          <button class="btn btn-danger" type="button"  data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</div>