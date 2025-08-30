import { Controller } from "@hotwired/stimulus"

// Connects to data-controller="donation-modal"
export default class extends Controller {
  connect() {
  }

  duplicate(){
    const DonationItem = this.element.querySelector('#donation-item');
    const donationModalBody = this.element.querySelector('#donationModalBody');

    const wrapper = document.createElement("div");
    wrapper.className = "donation-item-wrapper";

    const cloneDonationItem = DonationItem.cloneNode(true);

    cloneDonationItem.querySelectorAll("input").forEach(el =>{
      if(el.type === "file"){
        el.value = null
      }else{
        el.value = ""
      }
    })

    if(!cloneDonationItem.querySelector('#removeDonationItemButton')){
      const removeButton = document.createElement("button")
      removeButton.type = "button"
      removeButton.id = "removeDonationItemButton"
      removeButton.className = "btn btn-danger mt-3"
      removeButton.textContent ="Remove Donation Item"
      removeButton.addEventListener("click", () => wrapper.remove())
      cloneDonationItem.appendChild(removeButton)
    }

    const hr = document.createElement("hr");
    hr.className = "mt-4"

    wrapper.appendChild(hr);
    wrapper.appendChild(cloneDonationItem);

    donationModalBody.appendChild(wrapper)
  }
}
