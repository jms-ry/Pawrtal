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
            <h6 class="fw-bolder mt-2 text-uppercase font-monospace mt-1"><strong >Donation Details: </strong></h6>
            <div class="d-flex flex-column align-items-start ms-2">
              <div v-if="type === 'in-kind'" class="d-flex flex-column">
                <strong class="mt-2 ms-2 me-4">Item Description:  <span class="fw-lighter">{{ donationItemDescription }}</span></strong>
                <strong class="mt-2 ms-2 me-4">Item Quantity:  <span class="fw-lighter">{{ donationItemQuantity }}</span></strong>
                <strong class="mt-2 ms-2 me-4">Pick up Location:  <span class="fw-lighter">{{ pickUpLocation }}</span></strong>
                <strong class="mt-2 ms-2 me-4">Contact Person:  <span class="fw-lighter">{{ contactPerson }}</span></strong>
              </div>
              <div v-else class="d-flex flex-column">
                <strong class="mt-2 ms-2 me-4">Amount:  <span class="fw-lighter" id="amountSpan">{{ donationAmount }}</span></strong>
                <strong class="mt-2 ms-2 me-4">Payment Method:  <span class="fw-lighter" id="amountSpan">{{ donationPaymentMethod }}</span></strong>
                <strong class="mt-2 ms-2 me-4">Payment Status:  <span class="fw-lighter" id="amountSpan">{{ donationPaymentStatus }}</span></strong>
              </div>
            </div>
            <hr class="text-dark mt-4 mb-2">
            <h6 class="fw-bolder mt-2 text-uppercase font-monospace mt-1"><strong >Donation Progress: </strong></h6>
            <div class="d-flex flex-column align-items-start ms-2 mb-2">
              <div v-if="type=== 'monetary' && donationStatus === 'accepted'" class="d-flex align-items-center justify-content-between mt-3 px-3 w-100">
                <div class="d-flex flex-column align-items-center">
                  <div class="rounded-circle d-flex justify-content-center align-items-center mb-2 bg-info text-white" style="width: 50px; height: 50px;">
                    <i class="bi bi-hourglass-split fs-5"></i>
                  </div>
                  <strong class="text-center small">Submitted</strong>
                  <div class="text-muted small text-center">Submitted</div>
                </div>
                <div class="flex-grow-1 border-top border-2 mx-2 border-success" style="height: 2px; margin-bottom: 60px;">
                </div>
                <div class="d-flex flex-column align-items-center">
                  <div class="rounded-circle d-flex justify-content-center align-items-center mb-2 bg-warning text-dark" style="width: 50px; height: 50px;">
                    <i class="bi bi-check-circle fs-5"></i>
                  </div>
                  <strong class="text-center small">Accepted</strong>
                  <div class="text-muted small text-center">Accepted</div>
                </div>
              </div>
              <div v-else-if="type=== 'monetary' && donationStatus === 'cancelled'" class="d-flex align-items-center justify-content-between mt-3 px-3 w-100">
                <div class="d-flex flex-column align-items-center">
                  <div class="rounded-circle d-flex justify-content-center align-items-center mb-2 bg-info text-white" style="width: 50px; height: 50px;">
                    <i class="bi bi-hourglass-split fs-5"></i>
                  </div>
                  <strong class="text-center small">Submitted</strong>
                  <div class="text-muted small text-center">Submitted</div>
                </div>
                <div class="flex-grow-1 border-top border-2 mx-2 border-success" style="height: 2px; margin-bottom: 60px;">
                </div>
                <div class="d-flex flex-column align-items-center">
                  <div class="rounded-circle d-flex justify-content-center align-items-center mb-2 bg-warning text-dark" style="width: 50px; height: 50px;">
                      <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    </div>
                    <strong class="text-center small">Cancelled</strong>
                    <div class="text-muted small text-center">Cancelled</div>
                </div>
              </div>
              <div v-else-if="type === 'in-kind'" class="d-flex align-items-center justify-content-between mt-3 w-100">
                <div v-if="donationStatus === 'cancelled'" class="d-flex align-items-center justify-content-between mt-3 w-100">
                  <div class="d-flex flex-column align-items-center">
                    <div class="rounded-circle d-flex justify-content-center align-items-center mb-2 bg-info text-white" style="width: 50px; height: 50px;">
                      <i class="bi bi-hourglass-split fs-5"></i>
                    </div>
                    <strong class="text-center small">Pending</strong>
                    <div class="text-muted small text-center">Submitted</div>
                  </div>
                  <div class="flex-grow-1 border-top border-2 mx-2 border-warning" style="height: 2px; margin-bottom: 60px;"></div>
                  <div class="d-flex flex-column align-items-center">
                    <div class="rounded-circle d-flex justify-content-center align-items-center mb-2 bg-warning text-dark" style="width: 50px; height: 50px;">
                      <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    </div>
                    <strong class="text-center small">Cancelled</strong>
                    <div class="text-muted small text-center">Cancelled</div>
                  </div>
                </div>
                <div v-else class="d-flex align-items-center justify-content-between mt-3 w-100">
                  <div class="d-flex flex-column align-items-center">
                    <div class="rounded-circle d-flex justify-content-center align-items-center mb-2" :class="donationStatus === 'pending' || donationStatus === 'accepted' || donationStatus === 'rejected' ? 'bg-info text-white' : 'bg-secondary text-white'" style="width: 50px; height: 50px;">
                      <i class="bi bi-hourglass-split fs-5"></i>
                    </div>
                    <strong class="text-center small">Pending</strong>
                    <div class="text-muted small text-center">Submitted</div>
                  </div>
                  <div class="flex-grow-1 border-top border-2 mx-2" :class="donationStatus === 'pending' || donationStatus === 'accepted' || donationStatus === 'rejected' ? 'border-info' : 'border-secondary'" style="height: 2px; margin-bottom: 60px;">
                  </div>
                  <div class="d-flex flex-column align-items-center">
                    <div class="rounded-circle d-flex justify-content-center align-items-center mb-2" :class="donationStatus === 'pending' || donationStatus === 'accepted' || donationStatus === 'rejected' ? 'bg-primary text-white' : 'bg-secondary text-white'" style="width: 50px; height: 50px;">
                      <i class="bi bi-search fs-5"></i>
                    </div>
                    <strong class="text-center small">Under Review</strong>
                    <div class="text-muted small text-center">Reviewing</div>
                  </div>
                  <div class="flex-grow-1 border-top border-2 mx-2" :class="donationStatus === 'accepted' || donationStatus === 'rejected' ? 'border-primary' : 'border-secondary'" style="height: 2px; margin-bottom: 60px;">
                  </div>
                  <div class="d-flex flex-column align-items-center">
                    <div class="rounded-circle d-flex justify-content-center align-items-center mb-2" :class="donationStatus === 'accepted' ? 'bg-success text-white' : donationStatus === 'rejected' ? 'bg-danger text-white' : 'bg-secondary text-white'" style="width: 50px; height: 50px;">
                      <i :class="donationStatus === 'accepted' ? 'bi bi-check-circle fs-5' : donationStatus === 'rejected' ? 'bi bi-x-circle fs-5' : 'bi bi-clock fs-5'"></i>
                    </div>
                    <strong class="text-center small" v-if="donationStatus === 'accepted'">Accepted</strong>
                    <strong class="text-center small" v-else-if="donationStatus === 'rejected'">Rejected</strong>
                    <strong class="text-center small" v-else>Decision</strong>
                    <div class="text-muted small text-center" v-if="donationStatus === 'accepted'">Accepted</div>
                    <div class="text-muted small text-center" v-else-if="donationStatus === 'rejected'">Rejected</div>
                    <div class="text-muted small text-center" v-else>Pending</div>
                  </div>
                </div>
              </div>
            </div>
            <div v-if="type === 'in-kind'" class="card border-0 bg-info-subtle">
              <hr class="text-dark mt-4 mb-2">
              <h6 class="fw-bolder mt-2 text-uppercase font-monospace mt-1"><strong >Donation Image: </strong></h6>
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
            <div class="d-flex justify-content-start align-self-start" v-if="donationStatus === 'pending' && loggedUserIsAdminOrStaff === 'true' && type !== 'monetary'">
              <button class="btn btn-success me-1" type="button" :data-donation-id="donationId" data-bs-toggle="modal" data-bs-target="#acceptDonationModal">Accept Donation</button>
              <button class="btn btn-warning me-1" type="button" :data-donation-id="donationId" data-bs-toggle="modal" data-bs-target="#rejectDonationModal">Reject Donation</button>
            </div>
            <div class="d-flex justify-content-end align-self-end ms-auto">
              <button  v-if="donationStatus === 'pending' && isOwnedByLoggedUser === 'true' && type !== 'monetary'" type="button" class="btn btn-warning me-1" :data-donation-id="donationId" data-bs-toggle="modal" data-bs-target="#cancelDonationModal">Cancel Donation</button>
              <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            </div>
          </div>
          <!--Small Screen-->
          <div class="modal-footer bg-info-subtle d-md-none">
            <div v-if="loggedUserIsAdminOrStaff === 'true' && donationStatus === 'pending' && type !== 'monetary'" class="d-flex justify-content-center">
              <div class="d-flex flex-column">
                <div class="d-flex justify-content-center">
                  <button class="btn btn-success me-1" type="button" :data-donation-id="donationId" data-bs-toggle="modal" data-bs-target="#acceptDonationModal">Accept Donation</button>
                  <button class="btn btn-warning me-1" type="button" :data-donation-id="donationId" data-bs-toggle="modal" data-bs-target="#rejectDonationModal">Reject Donation</button>
                </div>
                <div class="mt-2 align-items-center d-flex justify-content-center">
                  <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
              </div>
            </div>
            <div v-else-if="isOwnedByLoggedUser === 'true'">
              <button  v-if="donationStatus === 'pending' && type !== 'monetary'" type="button" class="btn btn-warning me-1" :data-donation-id="donationId" data-bs-toggle="modal" data-bs-target="#cancelDonationModal">Cancel Donation</button>
              <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            </div>
            <div v-else>
              <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            </div>
          </div>
      </div>
    </div>
  </div>
  <CancelDonationModal />
  <AcceptDonationModal />
  <RejectDonationModal />
</template>

<script setup>
  import { ref, onMounted} from 'vue'
  import CancelDonationModal from './CancelDonationModal.vue'
  import AcceptDonationModal from '../../Donate/AcceptDonationModal.vue'
  import RejectDonationModal from '../../Donate/RejectDonationModal.vue'

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
  const donationAmount = ref(null)
  const donationPaymentMethod = ref(null)
  const donationPaymentStatus = ref(null)
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
      donationAmount.value = button.getAttribute('data-donation-amount-formatted')
      donationPaymentMethod.value = button.getAttribute('data-donation-payment-method')
      donationPaymentStatus.value = button.getAttribute('data-donation-payment-status')

      const donationTypeField = document.getElementById('donationType')
      donationTypeField.textContent = donationType.value + ' Donation'
      
      const donationStatusBadge =  document.getElementById('donationStatusLabel')
      donationStatusBadge.classList.remove(
        'text-bg-info',
        'text-bg-success',
        'text-bg-danger',
        'text-bg-primary',
        'text-bg-warning'
      )
      if(donationStatus.value === 'pending'){
        donationStatusBadge.classList.add('text-bg-info')
        const formattedStatus = donationStatus.value.charAt(0).toUpperCase() + donationStatus.value.slice(1)
        donationStatusBadge.innerHTML = `<i class="bi bi-hourglass-split me-1"></i> ${formattedStatus}`

      }else if(donationStatus.value === 'accepted'){
        donationStatusBadge.classList.add('text-bg-success')
        const formattedStatus = donationStatus.value.charAt(0).toUpperCase() + donationStatus.value.slice(1)
        donationStatusBadge.innerHTML = `<i class="bi bi-check-circle-fill me-1"></i> ${formattedStatus}`

      }else if(donationStatus.value === 'rejected'){
        donationStatusBadge.classList.add('text-bg-danger')
        const formattedStatus = donationStatus.value.charAt(0).toUpperCase() + donationStatus.value.slice(1)
        donationStatusBadge.innerHTML = `<i class="bi bi-x-circle-fill me-1"></i> ${formattedStatus}`

      }else{ //donation is cancelled
        donationStatusBadge.classList.add('text-bg-warning')
        const formattedStatus = donationStatus.value.charAt(0).toUpperCase() + donationStatus.value.slice(1)
        donationStatusBadge.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-1"></i> ${formattedStatus}`
      }

      isOwnedByLoggedUser.value = button.getAttribute('data-donation-is-owned-by-logged-user')
      loggedUserIsAdminOrStaff.value = button.getAttribute('data-donation-logged-user-is-admin-or-staff')
    })
  })

</script>