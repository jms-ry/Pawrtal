import { Controller } from "@hotwired/stimulus"

// Connects to data-controller="household"
export default class extends Controller {
  connect() {
    this.toggleNumberOfChildrenField();
    this.togglePetsFields();
  }

  toggleNumberOfChildrenField() {
    const numberOfChildrenDiv = this.element.querySelector('#number_of_children_div');
    const selectHaveChildren = this.element.querySelector('#floating_have_children');
    const numberOfChildrenInput = this.element.querySelector('#floating_number_of_children');

    if(selectHaveChildren.value === 'true') {
      numberOfChildrenDiv.classList.remove('d-none');
    }else{
      numberOfChildrenDiv.classList.add('d-none');
      numberOfChildrenInput.value = '';
    }
  }

  togglePetsFields() {
    const hasOthetPets = this.element.querySelector('#floating_has_other_pets');
    const currentPetsDiv = this.element.querySelector('#current_pets_div');
    const numberOfCurrentPetsDiv = this.element.querySelector('#number_of_current_pets_div');
    const currentPetsInput = this.element.querySelector('#floating_current_pets');
    const numberOfCurrentPetsInput = this.element.querySelector('#floating_number_of_current_pets');

    if(hasOthetPets.value === 'true') {
      currentPetsDiv.classList.remove('d-none');
      numberOfCurrentPetsDiv.classList.remove('d-none');
    }else{
      currentPetsDiv.classList.add('d-none');
      numberOfCurrentPetsDiv.classList.add('d-none');
      currentPetsInput.value = '';
      numberOfCurrentPetsInput.value = '';
    }
  }
}
