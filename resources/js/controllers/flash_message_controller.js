import { Controller } from "@hotwired/stimulus"

// Connects to data-controller="flash-message"
export default class extends Controller {
  connect() {
    setTimeout(() => {
      this.element.style.opacity = '0';
      this.element.addEventListener('transitionend', () => {
        this.element.remove();
      });
    }, 2000);
  }
}
