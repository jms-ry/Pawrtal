<div class="modal fade me-2" id="createInKindDonationModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="createInKindDonationModalLabel" aria-hidden="true" data-controller="donation-modal">
  <div class="modal-dialog modal-dialog-centered modal-dialog-sm-scrollable modal-lg">
    <div class="modal-content ">
      <div class="modal-header bg-info-subtle font-monospace">
        <i class="bi bi-box-seam-fill me-2 text-primary fs-2"></i>
        <h5 class="modal-title"><strong>Make In-kind Donation for the rescues!</strong></h5>
      </div>
      <form action="{{ route('donations.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body bg-info-subtle border-0" id="donationModalBody">
          <input type="hidden" name="user_id" class="form-control" value="{{ Auth::user()?->id }}">
          <input type="hidden" name="donation_type" class="form-control" value="in-kind">
          <input type="hidden" name="status" class="form-control" value="pending">
          <div id="donation-item" class="mb-4">
            <div class="row g-2 mt-2">
              <div class="col-12 col-md-6 form-floating">
                <input name="item_description[]" class="form-control" placeholder="Item Description" aria-label="Item Description" required></input>
                <label class="form-label fw-bold">Item Description</label>
              </div>
              <div class="col-12 col-md-6 form-floating">
                <input type="number" name="item_quantity[]" class="form-control" min="1" placeholder="Item Quantity" aria-label="Item Quantity" required>
                <label class="form-label fw-bold">Item Quantity</label>
              </div>
            </div>
            <div class="row g-2 mt-3">
              <div class="col-12 col-md-6 form-floating">
                <input type="text" name="pick_up_location[]" class="form-control" placeholder="Pick up Location" aria-label="Pick up Location">
                <label class="form-label fw-bold">Pick up Location</label>
              </div>
              <div class="col-12 col-md-6 form-floating">
                <input type="text" name="contact_person[]" class="form-control" placeholder="Contact Person" aria-label="Contact Person">
                <label class="form-label fw-bold">Contact Person</label>
              </div>
            </div>
            <div class="row g-2 mt-3">
              <div class="col-12">
                <label class="form-label fw-bold">Upload Donation Image (Proof)</label>
                <input type="file" name="donation_image[]" class="form-control" accept="image/*" required>
                <div class="form-text">Please upload a clear photo of the donation item(s) as proof.</div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-info-subtle d-flex justify-content-between">
          <div>
            <button class="btn btn-subtle-outline-primary me-1" type="button" id="addNewDonationItemButton" data-action="click->donation-modal#duplicate"> New Donation Item</button>
            <button class="btn btn-warning d-none me-1" type="button" id="removeNewDonationItemButton"> Remove Donation Item</button>
          </div>
          <div>
            <button class="btn btn-primary me-1" type="submit" >Submit Donation</button>
            <button class="btn btn-danger" type="button"  data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>