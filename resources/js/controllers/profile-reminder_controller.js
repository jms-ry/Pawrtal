import { Controller } from "@hotwired/stimulus"

// Connects to data-controller="profile-reminder"
export default class extends Controller {
  connect() {
    const profileReminderModal = document.getElementById('profileReminderModal');
    
    profileReminderModal.addEventListener('show.bs.modal', (event) => {
      const button = event.relatedTarget;

      const userId = button.getAttribute('data-user-id');
      const updateProfileButton = this.element.querySelector('#updateProfileBtn');
      updateProfileButton.href = `/users/${userId}`;
    });
  }
}
