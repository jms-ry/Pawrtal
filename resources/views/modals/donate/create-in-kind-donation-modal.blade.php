<div class="modal fade me-2" id="createInKindDonationModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="createInKindDonationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-sm-scrollable modal-lg">
    <div class="modal-content ">
      <div class="modal-header bg-info-subtle font-monospace">
        <i class="bi bi-box-seam-fill me-2 text-primary fs-2"></i>
        <h5 class="modal-title"><strong>Make In-kind Donation for the rescues!</strong></h5>
      </div>
      <form action="{{ route('donations.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body bg-info-subtle border-0">
          <input type="hidden" name="user_id" class="form-control" value="{{ Auth::user()->id }}">
          <input type="hidden" name="donation_type" class="form-control" value="in-kind">
          <input type="hidden" name="status" class="form-control" value="pending">
          <div id="donation-item">
            <div class="row g-2 mt-2">
              <div class="col-12 form-floating">
                <textarea name="item_description" id="floating_item_description" class="form-control" placeholder="Item Description" aria-label="Item Description" style="height: 100px" required></textarea>
                <label for="floating_item_description" class="form-label fw-bold">Item Description</label>
              </div>
            </div>
            <div class="row g-2 mt-3">
              <div class="col-12 col-md-6 form-floating">
                <input type="text" name="pick_up_location" class="form-control" placeholder="Pick up Location" aria-label="Pick up Location" id="floating_pick_up_location">
                <label for="floating_pick_up_location" class="form-label fw-bold">Pick up Location</label>
              </div>
              <div class="col-12 col-md-6 form-floating">
                <input type="text" name="contact_person" class="form-control" placeholder="Contact Person" aria-label="Contact Person" id="floating_contact_person">
                <label for="floating_contact_person" class="form-label fw-bold">Contact Person</label>
              </div>
            </div>
            <div class="row g-2 mt-3">
              <div class="col-12 col-md-5">
                <label for="item_quantity" class="form-label fw-bold">Input Item Quantity</label>
                <input type="number" name="item_quantity" id="item_quantity" class="form-control" min="1" required>
              </div>
              <div class="col-12 col-md-7">
                <label for="donation_image" class="form-label fw-bold">Upload Donation Image (Proof)</label>
                <input type="file" name="donation_image" id="donation_image" class="form-control" accept="image/*"required>
                <div class="form-text">Please upload a clear photo of the donation item(s) as proof.</div>
              </div>
            </div>
            
          </div>
        </div>
        <div class="modal-footer bg-info-subtle">
          <button class="btn btn-primary me-1" type="submit" >Submit Donation</button>
          <button class="btn btn-danger" type="button"  data-bs-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>