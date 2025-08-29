import { Controller } from "@hotwired/stimulus"

// Connects to data-controller="adoption-application"
export default class extends Controller {
  connect() {
    const adoptionForm = this.element.querySelector('#adoptionApplicationFormModal');

    adoptionForm.addEventListener('show.bs.modal',(event) =>{
      const button = event.relatedTarget;

      const adoptableName = button.getAttribute('data-adoptable-name');
      const adoptableNameStrong = this.element.querySelector('#adoption_form_adoptable-name');

      adoptableNameStrong.textContent = adoptableName;

      const userId = button.getAttribute('data-user-id');
      const userIdInputHidden = this.element.querySelector('#adoption_form_user_id');

      userIdInputHidden.value = userId;

      const rescueId = button.getAttribute('data-adoptable-id');
      const rescueIdInputHidden = this.element.querySelector('#adoption_form_rescue_id');

      rescueIdInputHidden.value = rescueId;
    });
  }
}
