import { Controller } from "@hotwired/stimulus"

// Connects to data-controller="delete-address-household"
export default class extends Controller {
  connect() {
    const deleteAddressModal = this.element.querySelector('#deleteAddressModal');

    deleteAddressModal.addEventListener('show.bs.modal',(event) => {
      const button = event.relatedTarget;

      const addressId = button.getAttribute('data-address-id');

      const deleteAddressForm = this.element.querySelector('#deleteAddressForm');
      deleteAddressForm.action = `/addresses/${addressId}`;
    });
  }
}
