import { Controller } from "@hotwired/stimulus"

// Connects to data-controller="delete-modal"
export default class extends Controller {
  connect() {
    const deleteModal = document.getElementById('deleteReportModal');

    deleteModal.addEventListener('show.bs.modal', (event) => {
      const button = event.relatedTarget;

      const type = button.getAttribute('data-report-type');
      const titleSpan = this.element.querySelector('#title');

      titleSpan.textContent = type === 'lost' ? 'Lost' : 'Found';

      const reportId = button.getAttribute('data-report-id');
      const deleteReportForm = this.element.querySelector('#deleteReportForm');
      deleteReportForm.action = `/reports/${reportId}`;
      
    });
  }
}
