import { Controller } from "@hotwired/stimulus"

// Connects to data-controller="report-switch"
export default class extends Controller {
  static targets = ["searchField", "createButton", "switch"];
  connect() {
    this.toggleFields();
  }

  toggleFields() {
    const isChecked = this.switchTarget.checked;
    const spanLabel = this.element.querySelector("#switchLabel");
    this.searchFieldTargets.forEach(el => {
      el.classList.toggle("d-none", isChecked);
      spanLabel.textContent = isChecked ? "Switch back to search reports " : "Switch to create a report!";
    });

    this.createButtonTarget.classList.toggle("d-none", !isChecked);
  }
}
