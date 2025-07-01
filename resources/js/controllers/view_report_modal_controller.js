import { Controller } from "@hotwired/stimulus"

// Connects to data-controller="view-report-modal"
export default class extends Controller {
  connect() {
    const viewReportModal = document.getElementById('viewReportModal');
    viewReportModal.addEventListener('show.bs.modal', (event) => {
      const button = event.relatedTarget;

      const type = button.getAttribute('data-report-type');
      const animal_name = button.getAttribute('data-report-animal-name');
      const species = button.getAttribute('data-report-species');
      const location = button.getAttribute('data-report-location');
      const date = button.getAttribute('data-report-seen-date');
      const breed = button.getAttribute('data-report-breed');
      const color = button.getAttribute('data-report-color');
      const sex = button.getAttribute('data-report-sex');
      const age_estimate = button.getAttribute('data-report-age-estimate');
      const size = button.getAttribute('data-report-size');
      const distinctive_features = button.getAttribute('data-report-distinctive-features');
      const condition = button.getAttribute('data-report-condition');
      const temporary_shelter = button.getAttribute('data-report-temporary-shelter');
      const full_name = button.getAttribute('data-report-owner-name');
      const contact_number = button.getAttribute('data-report-owner-contact-number');
      const email = button.getAttribute('data-report-owner-email');
      const status_label = button.getAttribute('data-report-status-label');
      
      const lostFields = viewReportModal.querySelector('.lost-fields');
      const foundFields = viewReportModal.querySelectorAll('.found-fields');

      if (type === 'lost')
      {
        lostFields.classList.remove('d-none');
        foundFields.forEach(foundField => {
          foundField.classList.add('d-none');
        })

        const animalNameSpan = this.element.querySelector('#animalNameSpan');
        const lastSeenLocationSpan = this.element.querySelector('#lastSeenLocationSpan');
        const lastSeenDateSpan = this.element.querySelector('#lastSeenDateSpan');

        animalNameSpan.textContent = animal_name;
        lastSeenLocationSpan.textContent = location;
        lastSeenDateSpan.textContent = date;
      }
      else
      {
        foundFields.forEach(foundField => {
          foundField.classList.remove('d-none');
        })
        lostFields.classList.add('d-none');

        const foundLocationSpan = this.element.querySelector('#foundLocationSpan');
        const foundDateSpan = this.element.querySelector('#foundDateSpan');
        const condtionSpan = this.element.querySelector('#conditionSpan');
        const temporaryShelterSpan = this.element.querySelector('#temporaryShelterSpan');

        condtionSpan.textContent = condition;
        temporaryShelterSpan.textContent = temporary_shelter;
        foundLocationSpan.textContent = location;
        foundDateSpan.textContent = date;
      }
      
      const typeSpan = this.element.querySelector('#reportType');
      typeSpan.textContent = type + " " + species +" "+ "Details";

      const breedSpan = this.element.querySelector('#breedSpan');
      breedSpan.textContent = breed;

      const colorSpan = this.element.querySelector('#colorSpan');
      colorSpan.textContent = color;

      const sexSpan = this.element.querySelector('#sexSpan');
      sexSpan.textContent = sex;

      const ageEstimateSpan = this.element.querySelector('#ageEstimateSpan');
      ageEstimateSpan.textContent = age_estimate;

      const sizeSpan = this.element.querySelector('#sizeSpan');
      sizeSpan.textContent = size;

      const distinctiveFeaturesSpan = this.element.querySelector('#distinctiveFeaturesSpan');
      distinctiveFeaturesSpan.textContent = distinctive_features;

      const statusLabel = this.element.querySelector('#reportStatusLabel');
      const status = button.getAttribute('data-report-status');
      const activeFields = viewReportModal.querySelector('.active-fields');
      if( status === 'active')
      {
        activeFields.classList.remove('d-none');
        statusLabel.classList.remove('text-bg-success');
        statusLabel.classList.add('text-bg-warning');

        const contactNumber = this.element.querySelector('#contactNumberSpan');
        contactNumber.textContent = contact_number;

        const emailSpan = this.element.querySelector('#emailAddressSpan');
        emailSpan.textContent = email;
        
        statusLabel.innerHTML = `<i class="bi bi-hourglass-split me-1"></i> ${status_label}`;
      }
      else
      {
        activeFields.classList.add('d-none');
        statusLabel.classList.remove('text-bg-warning');
        statusLabel.classList.add('text-bg-success');
        statusLabel.innerHTML = `<i class="bi bi-check-circle-fill me-1"></i> ${status_label}`;
      }
      
      const fullName = this.element.querySelector('#nameSpan');
      fullName.textContent = full_name;
    });
  }
}
