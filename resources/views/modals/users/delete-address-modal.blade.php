<div class="modal fade me-2" id="deleteAddressModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteAddressModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-sm-scrollable">
    <div class="modal-content ">
      <form id="deleteAddressForm" method="POST" class="">
        @csrf
        @method('DELETE')
        <div class="modal-body bg-info-subtle border-0">
          <div class="d-flex d-flex-row justify-content-start align-items-center mt-4">
            <i class="bi bi-trash3-fill me-3 text-danger fs-2"></i>
            <p class="fw-bold font-monospace mt-2 fs-5 text-start">Are you sure you wanna delete your address?</p>
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