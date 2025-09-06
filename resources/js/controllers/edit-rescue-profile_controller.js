import { Controller } from "@hotwired/stimulus"

// Connects to data-controller="edit-rescue-profile"
export default class extends Controller {
  connect() {
    const updateRescueProfileModal = this.element.querySelector('#editRescueProfileModal');

    updateRescueProfileModal.addEventListener('show.bs.modal', (event) => {
      const button = event.relatedTarget;

      const name = button.getAttribute('data-rescue-name');
      const profile_image_url = button.getAttribute('data-rescue-profile-image');

      const profileImageDiv = this.element.querySelector('#rescueProfileImageDiv');
      const rescueProfileImage = this.element.querySelector('#rescueProfileImage');

      profileImageDiv.classList.remove('d-none');

      rescueProfileImage.src = profile_image_url;
      rescueProfileImage.alt = name;
    })
  }
}
