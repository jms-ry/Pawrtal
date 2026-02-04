<template>
  <div class="modal fade me-2" id="monetaryDonationModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="monetaryDonationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-sm-scrollable">
      <div class="modal-content">
        <div class="modal-header bg-info-subtle font-monospace">
          <i class="bi bi-wallet-fill me-2 text-primary fs-2"></i>
          <h5 class="modal-title" id="monetaryDonationModalLabel">
            <strong>Make a Monetary Donation</strong>
          </h5>
        </div>

        <div class="modal-body">
          <h6 class="fw-bold mb-3 text-dark">
            Step 1: Enter or select amount to donate
          </h6>
          <div class="mb-3">
            <label class="form-label fw-semibold">Custom Amount (₱)</label>
            <input type="number" class="form-control" min="1" placeholder="Enter amount to donate" v-model="amount">
          </div>
          <div class="mb-4">
            <label class="form-label fw-semibold">Or select an amount</label>
            <div class="d-flex flex-wrap gap-2">
              <button v-for="preset in presetAmounts" :key="preset" type="button" :class="amount === preset ? 'btn-primary' : 'btn-outline-primary'" @click="amount = preset" class="btn">
                ₱{{ preset }}
              </button>
            </div>
          </div>
          <hr>
          <h6 class="fw-bold mb-3 text-dark">
            Step 2: Select donation method
          </h6>
          <div class="d-grid gap-2">
            <!-- GCash -->
            <button type="button" class="btn d-flex align-items-center justify-content-between" :class="method === 'gcash' ? 'btn-dark' : 'btn-outline-dark'" @click="method = 'gcash'">
              <span class="d-flex align-items-center">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/52/GCash_logo.svg/512px-GCash_logo.svg.png?20230830061433" alt="GCash" class="me-2" style="height: 22px">
              </span>
              <i class="bi bi-check-circle-fill" v-if="method === 'gcash'"></i>
            </button>
            <!-- Maya -->
            <button type="button" class="btn d-flex align-items-center justify-content-between" :class="method === 'maya' ? 'btn-dark' : 'btn-outline-dark'" @click="method = 'maya'">
              <span class="d-flex align-items-center">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/e6/Maya_logo.svg/128px-Maya_logo.svg.png?20220502032254" alt="Maya" class="me-2" style="height: 22px">
              </span>
              <i class="bi bi-check-circle-fill" v-if="method === 'maya'"></i>
            </button>
            <!-- Credit Card -->
            <button type="button" class="btn d-flex align-items-center justify-content-between" :class="method === 'card' ? 'btn-dark' : 'btn-outline-dark'" @click="method = 'card'">
              <span>
                <i class="bi bi-credit-card-fill me-2"></i>
                Credit / Debit Card
              </span>
              <i class="bi bi-check-circle-fill" v-if="method === 'card'"></i>
            </button>
          </div>
          <!-- Card details -->
          <div v-if="method === 'card'" class="mt-4">
            <h6 class="fw-bold mb-3 text-primary">
              Enter Card Details
            </h6>
            <div class="mb-3">
              <label class="form-label">Card Number</label>
              <input type="text" class="form-control" placeholder="1234 5678 9012 3456" v-model="card.number"/>
            </div>
            <div class="row">
              <div class="col-6 mb-3">
                <label class="form-label">Expiry Date</label>
                <input type="text" class="form-control" placeholder="MM / YY" v-model="card.expiry"/>
              </div>
              <div class="col-6 mb-3">
                <label class="form-label">CVC</label>
                <input type="text" class="form-control" placeholder="123" v-model="card.cvc"/>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Cardholder Name</label>
              <input type="text" class="form-control" placeholder="Juan Dela Cruz" v-model="card.name"/>
            </div>
          </div>
        </div>

        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-danger me-1" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" :disabled="!canProceed">Proceed</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { ref, computed } from 'vue'

  const amount = ref(null)
  const method = ref(null)

  const card = ref({
    number: '',
    expiry: '',
    cvc: '',
    name: ''
  })

  const presetAmounts = [10, 50, 100, 250, 500, 1000]

  const canProceed = computed(() => {
    if (!amount.value || !method.value) return false

    if (method.value === 'card') {
      return (
        card.value.number &&
        card.value.expiry &&
        card.value.cvc &&
        card.value.name
      )
    }

    return true
  })
</script>