import { Controller } from "@hotwired/stimulus"

// Connects to data-controller="update-lost-report"
export default class extends Controller {
  connect() {
    const updateLostReportModal = this.element.querySelector('#updateLostReportModal');

    updateLostReportModal.addEventListener('show.bs.modal', (event) =>{
      const button = event.relatedTarget;

      const typeFormatted = button.getAttribute('data-report-type-formatted');
      const updateModalTitle = document.querySelector('#modal-title-span');

      updateModalTitle.textContent = typeFormatted;

      const image_url = button.getAttribute('data-report-image');

      const reportImage = this.element.querySelector('#reportImage');
      reportImage.src = image_url;
    })
  }
}
