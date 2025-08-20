import { Controller } from "@hotwired/stimulus"

// Connects to data-controller="rescue-switch"
export default class extends Controller {
  static targets = ["searchField", "createButton", "switch"];
  connect() {
    this.toggle();
  }

  toggle(){
    const isChecked = this.switchTarget.checked;
    const spanLabel = this.element.querySelector("#switchLabel");
    this.searchFieldTargets.forEach(el => {
      el.classList.toggle("d-none", isChecked);
      spanLabel.textContent = isChecked ? "Switch back to search rescues " : "Switch to create a rescue profile!";
    });

    this.createButtonTarget.classList.toggle("d-none", !isChecked);
  }
}
