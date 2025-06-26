import { Controller } from "@hotwired/stimulus"

// Connects to data-controller="switch-search-button"
export default class extends Controller {
  static targets = ["searchField", "matchButton", "switch"];

  connect() {
    
  }

  toggleFields() {
    const isChecked = this.switchTarget.checked;
    const spanLabel = this.element.querySelector("#switchLabel");
    this.searchFieldTargets.forEach(el => {
      el.classList.toggle("d-none", isChecked);
      spanLabel.textContent = isChecked ? "Switch back to manual search " : "Switch to AI recommendation?";
    });

    this.matchButtonTarget.classList.toggle("d-none", !isChecked);
  }
}
