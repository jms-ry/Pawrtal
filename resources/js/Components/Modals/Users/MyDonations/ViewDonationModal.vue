<template>
  <div class="modal fade me-2" id="viewDonationModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="viewDonationModalLabel">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-body border-0 bg-info-subtle">
          <div class="d-flex justify-content-between">
            <h3 class="fs-5 fw-bolder mt-2 text-uppercase font-monospace"><strong id="donationType"></strong></h3>
            <span class="badge d-flex justify-content-center align-items-center text-dark fw-bold" id="donationStatusLabel"> Status</span>
          </div>
          <div class="card border-0 bg-info-subtle">
            <hr class="text-dark mt-3 mb-2">
            <div class="d-flex flex-column align-items-start ms-2">
              <div v-if="type === 'in-kind'" class="d-flex flex-column">
                <strong class="mt-2 ms-2 me-4">Item Description:  <span class="fw-lighter">{{ donationItemDescription }}</span></strong>
                <strong class="mt-2 ms-2 me-4">Item Quantity:  <span class="fw-lighter">{{ donationItemQuantity }}</span></strong>
                <strong class="mt-2 ms-2 me-4">Pick up Location:  <span class="fw-lighter">{{ pickUpLocation }}</span></strong>
                <strong class="mt-2 ms-2 me-4">Contact Person:  <span class="fw-lighter">{{ contactPerson }}</span></strong>
              </div>
              <div v-else class="d-flex flex-column">
                <strong class="mt-2 ms-2 me-4">Amount:  <span class="fw-lighter" id="amountSpan"></span></strong>
              </div>
            </div>
            <div v-if="type === 'in-kind'" class="card border-0 bg-info-subtle">
              <hr class="text-dark mt-4 mb-2">
              <h3 class="fs-5 fw-bolder mt-2 text-uppercase font-monospace"><strong >Donation Image</strong></h3>
              <div class="d-flex flex-column align-items-start ms-2 mb-2">
                <div class="mb-2 mt-2 justify-content-center">
                  <img :src="donationImage" class="w-100 h-100 object-fit-cover rounded-4" style="max-height: 300px; max-width: 100%;">
                </div>
              </div>
            </div>
          </div>
        </div>
        <!--Large Screen-->
          <div class="modal-footer bg-info-subtle d-none d-md-flex">
            <div class="d-flex justify-content-start align-self-start" v-if="donationStatus === 'pending' && loggedUserIsAdminOrStaff === 'true'">
              <button class="btn btn-success me-1" type="button" >Accept Donation</button>
              <button class="btn btn-warning me-1" type="button" >Reject Donation</button>
            </div>
            <div class="d-flex justify-content-end align-self-end ms-auto">
              <button  v-if="donationStatus === 'pending' && isOwnedByLoggedUser === 'true'" type="button" class="btn btn-warning" :data-donation-id="donationId" data-bs-toggle="modal" data-bs-target="#cancelDonationModal">Cancel Donation</button>
              <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            </div>
          </div>
          <!--Small Screen-->
          <div class="modal-footer bg-info-subtle d-md-none d-flex justify-content-center">
            <div>
              <button class="btn btn-success me-1" type="button" >Accept Donation</button>
              <button class="btn btn-warning me-1" type="button" >Reject Donation</button>
            </div>
            <div>
              <button  v-if="donationStatus === 'pending' && isOwnedByLoggedUser === 'true'" type="button" class="btn btn-warning" :data-donation-id="donationId" data-bs-toggle="modal" data-bs-target="#cancelDonationModal">Cancel Donation</button>
              <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            </div>
          </div>
      </div>
    </div>
  </div>
  <CancelDonationModal />
</template>

<script setup>
  import { ref, onMounted} from 'vue'
  import CancelDonationModal from './CancelDonationModal.vue'

  const donationId = ref(null)
  const donationType = ref(null)
  const donationStatus = ref(null)
  const donationItemDescription = ref(null)
  const donationItemQuantity = ref(null)
  const pickUpLocation = ref(null)
  const contactPerson = ref(null)
  const type = ref(null)
  const donationImage = ref(null)
  const isOwnedByLoggedUser = ref(null)
  const loggedUserIsAdminOrStaff = ref(null)
  onMounted(() => {
    const modalEl = document.getElementById('viewDonationModal')

    modalEl.addEventListener('show.bs.modal', (event) => { 
      const button = event.relatedTarget

      donationId.value = button.getAttribute('data-donation-id')
      donationType.value = button.getAttribute('data-donation-type-formatted')
      donationItemDescription.value = button.getAttribute('data-donation-item-description')
      donationItemQuantity.value = button.getAttribute('data-donation-item-quantity')
      pickUpLocation.value = button.getAttribute('data-donation-pick-up-location')
      contactPerson.value = button.getAttribute('data-donation-contact-person')
      donationStatus.value = button.getAttribute('data-donation-status')
      type.value = button.getAttribute('data-donation-type')
      donationImage.value = button.getAttribute('data-donation-image')
      
      const donationTypeField = document.getElementById('donationType')
      donationTypeField.textContent = donationType.value + ' Donation'
      
      const donationStatusBadge =  document.getElementById('donationStatusLabel')
      if(donationStatus.value === 'pending'){
        donationStatusBadge.classList.add('text-bg-info')
        const formattedStatus = donationStatus.value.charAt(0).toUpperCase() + donationStatus.value.slice(1)
        donationStatusBadge.innerHTML = `<i class="bi bi-hourglass-split me-1"></i> ${formattedStatus}`

      }else if(donationStatus.value === 'approved'){
        donationStatusBadge.classList.add('text-bg-success')
        const formattedStatus = donationStatus.value.charAt(0).toUpperCase() + donationStatus.value.slice(1)
        donationStatusBadge.innerHTML = `<i class="bi bi-check-circle-fill me-1"></i> ${formattedStatus}`

      }else if(donationStatus.value === 'picked-up'){
        donationStatusBadge.classList.add('text-bg-success')
        const formattedStatus = donationStatus.value.charAt(0).toUpperCase() + donationStatus.value.slice(1)
        donationStatusBadge.innerHTML = `<i class="bi bi-check-circle-fill me-1"></i> ${formattedStatus}`

      }else if(donationStatus.value === 'rejected'){
        donationStatusBadge.classList.add('text-bg-danger')
        const formattedStatus = donationStatus.value.charAt(0).toUpperCase() + donationStatus.value.slice(1)
        donationStatusBadge.innerHTML = `<i class="bi bi-x-circle me-1"></i> ${formattedStatus}`

      }else if(donationStatus.value === 'archived'){
        donationStatusBadge.classList.add('text-bg-light')
        const formattedStatus = donationStatus.value.charAt(0).toUpperCase() + donationStatus.value.slice(1)
        donationStatusBadge.innerHTML = `<i class="bi bi-archive-fill me-1"></i> ${formattedStatus}`
        
      }else{
        donationStatusBadge.classList.add('text-bg-warning')
        const formattedStatus = donationStatus.value.charAt(0).toUpperCase() + donationStatus.value.slice(1)
        donationStatusBadge.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-1"></i> ${formattedStatus}`
      }

      isOwnedByLoggedUser.value = button.getAttribute('data-donation-is-owned-by-logged-user')
      loggedUserIsAdminOrStaff.value = button.getAttribute('data-donation-logged-user-is-admin-or-staff')
    })
  })

</script>