import { Controller } from "@hotwired/stimulus"

// Connects to data-controller="update-report-modal"
export default class extends Controller {
  connect() {
    const updateReportModal = document.getElementById('updateReportModal');

    updateReportModal.addEventListener('show.bs.modal', (event) => {
      const button = event.relatedTarget;

      const id = button.getAttribute('data-report-id');
      const type = button.getAttribute('data-report-type');
      const status = button.getAttribute('data-report-status');
      const animal_name = button.getAttribute('data-report-animal-name');
      const species = button.getAttribute('data-report-species');
      const breed = button.getAttribute('data-report-breed');
      const color = button.getAttribute('data-report-color');
      const sex = button.getAttribute('data-report-sex');
      const age_estimate = button.getAttribute('data-report-age-estimate');
      const size = button.getAttribute('data-report-size');
      const distinctive_features = button.getAttribute('data-report-distinctive-features');
      const location = button.getAttribute('data-report-location');
      const last_seen_date = button.getAttribute('data-report-last-seen-date');
      const found_date = button.getAttribute('data-report-found-date');
      const condition = button.getAttribute('data-report-condition');
      const temporary_shelter = button.getAttribute('data-report-temporary-shelter');
      const image_url = button.getAttribute('data-report-image');

      const updateLostAnimalReportForm = this.element.querySelector('#updateLostAnimalReportForm');
      const updateFoundAnimalReportForm = this.element.querySelector('#updateFoundAnimalReportForm');

      const typeFormatted = button.getAttribute('data-report-type-formatted');
      const updateModalTitle = this.element.querySelector('#modal-title-span');

      updateModalTitle.textContent = typeFormatted;

      if (type === 'lost')
      {
        updateLostAnimalReportForm.classList.remove('d-none');
        updateFoundAnimalReportForm.classList.add('d-none');
        updateLostAnimalReportForm.action = `/reports/${id}`;
      }
      else
      {
        updateFoundAnimalReportForm.classList.remove('d-none');
        updateLostAnimalReportForm.classList.add('d-none');
        updateFoundAnimalReportForm.action = `/reports/${id}`;
      }

      //Lost Animal Form Fields
      const animal_field = this.element.querySelector('#floating_animal_name');
      const species_field = this.element.querySelector('#floating_animal_species');
      const breed_field = this.element.querySelector('#floating_animal_breed');
      const color_field = this.element.querySelector('#floating_animal_color');
      const sex_select = this.element.querySelector('#floating_animal_sex');
      const age_estimate_field = this.element.querySelector('#floating_animal_age_estimate');
      const size_select = this.element.querySelector('#floating_animal_size');
      const distinctive_features_field = this.element.querySelector('#floating_animal_distinctive_features');
      const last_seen_location_field = this.element.querySelector('#floating_animal_last_seen_location');
      const last_seen_date_field = this.element.querySelector('#floating_animal_last_seen_date');
      const status_select = this.element.querySelector('#report_status');
      const reportImage = this.element.querySelector('#reportImage');

      animal_field.value = animal_name;
      species_field.value = species;
      breed_field.value = breed;
      color_field.value = color;
      sex_select.value = sex;
      age_estimate_field.value = age_estimate; 
      size_select.value = size;
      distinctive_features_field.value = distinctive_features;
      last_seen_location_field.value = location;
      last_seen_date_field.value = last_seen_date;
      status_select.value = status;
      reportImage.src = image_url;

      //Found Animal Form Fields
      const found_species_field = this.element.querySelector('#floating_animal_species_found');
      const found_breed_field = this.element.querySelector('#floating_animal_breed_found');
      const found_color_field = this.element.querySelector('#floating_animal_color_found');
      const found_sex_select = this.element.querySelector('#floating_animal_sex_found');
      const found_age_estimate_field = this.element.querySelector('#floating_animal_age_estimate_found');
      const found_size_select = this.element.querySelector('#floating_animal_size_found');
      const found_distinctive_features_field = this.element.querySelector('#floating_animal_distinctive_features_found');
      const found_temporary_shelter_field = this.element.querySelector('#floating_animal_temporary_shelter_found');
      const found_condition_field = this.element.querySelector('#floating_animal_condition_found');
      const found_location_field = this.element.querySelector('#floating_animal_found_location');
      const found_date_field = this.element.querySelector('#floating_animal_found_date');
      const reportImageFound = this.element.querySelector('#reportImageFound');
      const found_status_select = this.element.querySelector('#report_status_found');

      found_species_field.value = species;
      found_breed_field.value = breed;
      found_color_field.value = color;
      found_sex_select.value = sex;
      found_age_estimate_field.value = age_estimate; 
      found_size_select.value = size;
      found_distinctive_features_field.value = distinctive_features;
      found_temporary_shelter_field.value = temporary_shelter;
      found_condition_field.value = condition;
      found_location_field.value = location;
      found_date_field.value = found_date;
      reportImageFound.src = image_url;
      found_status_select.value = status;
    });
  }
}
