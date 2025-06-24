import { Controller } from "@hotwired/stimulus"

// Connects to data-controller="want-to-help-btn"
export default class extends Controller {
  connect() {
    console.log("WantToHelpBtnController connected");

    const wantToHelpButton = this.element.querySelectorAll('.btn-help-scroll');

    wantToHelpButton.forEach(wantToHelpButton => {
      wantToHelpButton.addEventListener('click', (e) => {
        e.preventDefault();
        const wantToHelpSection = this.element.querySelector(('#how-you-can-help'));
        if (wantToHelpSection) {
          wantToHelpSection.scrollIntoView({ behavior: 'smooth' });
        }
      });
    });
  }
}
