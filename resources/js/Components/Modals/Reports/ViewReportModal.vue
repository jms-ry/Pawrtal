<template>
  <div class="modal fade me-2" id="viewReportModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="viewReportModalLabel">
   <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-body border-0 bg-info-subtle">
          <div class="d-flex justify-content-between">
            <h3 class="fs-5 fw-bolder mt-2 text-uppercase font-monospace"><strong id="reportType"></strong></h3>
            <span class="badge d-flex justify-content-center align-items-center" id="reportStatusLabel"></span>
          </div>
          <div class="card border-0 bg-info-subtle">
            <hr class="text-dark mt-2 mb-2">
            <div class="d-flex flex-column align-items-start ms-2">
              <div data-report-type="lost" class="d-flex flex-column lost-fields d-none">
                <strong class="mt-2 ms-2">Name: <span class="fw-lighter" id="animalNameSpan"></span></strong>
                <strong class="mt-2 ms-2">Last Seen at: <span class="fw-lighter" id="lastSeenLocationSpan"></span></strong>
                <strong class="mt-2 ms-2">Last Seen on: <span class="fw-lighter" id="lastSeenDateSpan"></span></strong>
              </div>
              <div data-report-type="found" class="d-flex flex-column found-fields d-none">
                <strong class="mt-2 ms-2">Found at: <span class="fw-lighter" id="foundLocationSpan"></span></strong>
                <strong class="mt-2 ms-2">Found on: <span class="fw-lighter" id="foundDateSpan"></span></strong>
              </div>
              <strong class="mt-2 ms-2">Breed: <span class="fw-lighter" id="breedSpan"></span></strong>
              <strong class="mt-2 ms-2">Color: <span class="fw-lighter" id="colorSpan"></span></strong>
              <strong class="mt-2 ms-2">Sex: <span class="fw-lighter" id="sexSpan"></span></strong>
              <strong class="mt-2 ms-2">Age Estimate: <span class="fw-lighter" id="ageEstimateSpan"></span></strong>
              <strong class="mt-2 ms-2">Size: <span class="fw-lighter" id="sizeSpan"></span></strong>
              <strong class="mt-2 ms-2">Distinctive Features: <span class="fw-lighter" id="distinctiveFeaturesSpan"></span></strong>
              <div data-report-type="found" class="d-flex flex-column found-fields d-none">
                <strong class="mt-2 ms-2">Condition: <span class="fw-lighter" id="conditionSpan"></span></strong>
                <strong class="mt-2 ms-2">Temporary Shelter: <span class="fw-lighter" id="temporaryShelterSpan"></span></strong>
              </div>
            
            </div>
          </div>
          <div class="card border-0 bg-info-subtle">
            <hr class="text-dark mt-4 mb-2">
            <h3 class="fs-5 fw-bolder mt-2 text-uppercase font-monospace"><strong >Owner's Info</strong></h3>
            <div class="d-flex flex-column align-items-start ms-2 mb-2">
              <strong class="mt-2 ms-2">Name: <span class="fw-lighter" id="nameSpan"></span></strong>
              <div class="d-flex flex-column d-none" id="active-fields">
                <strong class="mt-2 ms-2">Phone: <span class="fw-lighter" id="contactNumberSpan"></span></strong>
                <strong class="mt-2 ms-2">Email Address: <span class="fw-lighter" id="emailAddressSpan"></span></strong>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer border-0 bg-info-subtle">
          <div class="align-self-start d-none" id="deleteReportButtonDiv">
            <button type="button" class="btn btn-danger deleteReportButton" data-bs-toggle="modal" data-bs-target="#deleteReportModal"
            data-report-type=""
            data-report-id="">Delete Report</button>
          </div>
          <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>