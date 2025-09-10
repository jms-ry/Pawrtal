import { Controller } from "@hotwired/stimulus"

// Connects to data-controller="update-found-report"
export default class extends Controller {
  connect() {
    const updateFoundReportModal = this.element.querySelector('#updateFoundReportModal');

    updateFoundReportModal.addEventListener('show.bs.modal', (event) =>{
      const button = event.relatedTarget;

      const typeFormatted = button.getAttribute('data-report-type-formatted');
      const updateModalTitle = this.element.querySelector('#found-modal-title-span');

      updateModalTitle.textContent = typeFormatted;

      const image_url = button.getAttribute('data-report-image');
      const reportImage = this.element.querySelector('#reportImageFound');
      reportImage.src = image_url;
    })
  }
}

