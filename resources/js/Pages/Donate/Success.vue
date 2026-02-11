<template>
  <Head title="Payment Successful" />
  <AppLayout>
    <div class="container mt-5 mb-5">
      <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
          <div class="card border-0 shadow-sm">
            <div class="card-body text-center p-5">
              
              <!-- Success Icon -->
              <div class="mb-4">
                <div class="success-checkmark">
                  <div class="check-icon">
                    <span class="icon-line line-tip"></span>
                    <span class="icon-line line-long"></span>
                    <div class="icon-circle"></div>
                    <div class="icon-fix"></div>
                  </div>
                </div>
              </div>

              <!-- Success Message -->
              <h2 class="text-success fw-bold mb-3">Payment Successful!</h2>
              <p class="text-muted mb-4">
                Thank you for your generous donation. Your contribution helps us make a difference.
              </p>

              <!-- Donation Details (if available) -->
              <div v-if="donation" class="bg-light rounded p-4 mb-4 text-start">
                <h5 class="fw-semibold mb-3">Donation Details</h5>
                <div class="row mb-2">
                  <div class="col-6 text-muted">Amount:</div>
                  <div class="col-6 fw-semibold">₱{{ formatAmount(donation.amount) }}</div>
                </div>
                <div class="row mb-2">
                  <div class="col-6 text-muted">Date:</div>
                  <div class="col-6 fw-semibold">{{ formatDate(donation.donation_date) }}</div>
                </div>
                <div class="row mb-2">
                  <div class="col-6 text-muted">Payment Method:</div>
                  <div class="col-6 fw-semibold text-uppercase">{{ donation.payment_method }}</div>
                </div>
                <div v-if="donation.transaction_reference" class="row">
                  <div class="col-6 text-muted">Reference:</div>
                  <div class="col-6 fw-semibold small">{{ donation.transaction_reference }}</div>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="d-grid gap-2">
                <a 
                  href="/users/my-donations" 
                  class="btn btn-primary btn-lg"
                >
                  <i class="bi bi-list-ul me-2"></i>
                  View My Donations
                </a>
                <a 
                  href="/" 
                  class="btn btn-outline-secondary"
                >
                  Back to Home
                </a>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '../../Layouts/AppLayout.vue';

// Props from controller
const props = defineProps({
  donation: {
    type: Object,
    default: null
  }
});

// Format amount with thousand separators
const formatAmount = (amount) => {
  return parseFloat(amount).toLocaleString('en-PH', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  });
};

// Format date
const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-PH', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};
</script>

<style scoped>
/* Success Checkmark Animation */
.success-checkmark {
  width: 80px;
  height: 80px;
  margin: 0 auto;
}

.success-checkmark .check-icon {
  width: 80px;
  height: 80px;
  position: relative;
  border-radius: 50%;
  box-sizing: content-box;
  border: 4px solid #198754;
}

.success-checkmark .check-icon::before {
  top: 3px;
  left: -2px;
  width: 30px;
  transform-origin: 100% 50%;
  border-radius: 100px 0 0 100px;
}

.success-checkmark .check-icon::after {
  top: 0;
  left: 30px;
  width: 60px;
  transform-origin: 0 50%;
  border-radius: 0 100px 100px 0;
  animation: rotate-circle 4.25s ease-in;
}

.success-checkmark .check-icon::before,
.success-checkmark .check-icon::after {
  content: '';
  height: 100px;
  position: absolute;
  background: #fff;
  transform: rotate(-45deg);
}

.success-checkmark .check-icon .icon-line {
  height: 5px;
  background-color: #198754;
  display: block;
  border-radius: 2px;
  position: absolute;
  z-index: 10;
}

.success-checkmark .check-icon .icon-line.line-tip {
  top: 46px;
  left: 14px;
  width: 25px;
  transform: rotate(45deg);
  animation: icon-line-tip 0.75s;
}

.success-checkmark .check-icon .icon-line.line-long {
  top: 38px;
  right: 8px;
  width: 47px;
  transform: rotate(-45deg);
  animation: icon-line-long 0.75s;
}

.success-checkmark .check-icon .icon-circle {
  top: -4px;
  left: -4px;
  z-index: 10;
  width: 80px;
  height: 80px;
  border-radius: 50%;
  position: absolute;
  box-sizing: content-box;
  border: 4px solid rgba(25, 135, 84, 0.5);
}

.success-checkmark .check-icon .icon-fix {
  top: 8px;
  width: 5px;
  left: 26px;
  z-index: 1;
  height: 85px;
  position: absolute;
  transform: rotate(-45deg);
  background-color: #fff;
}

@keyframes rotate-circle {
  0% {
    transform: rotate(-45deg);
  }
  5% {
    transform: rotate(-45deg);
  }
  12% {
    transform: rotate(-405deg);
  }
  100% {
    transform: rotate(-405deg);
  }
}

@keyframes icon-line-tip {
  0% {
    width: 0;
    left: 1px;
    top: 19px;
  }
  54% {
    width: 0;
    left: 1px;
    top: 19px;
  }
  70% {
    width: 50px;
    left: -8px;
    top: 37px;
  }
  84% {
    width: 17px;
    left: 21px;
    top: 48px;
  }
  100% {
    width: 25px;
    left: 14px;
    top: 45px;
  }
}

@keyframes icon-line-long {
  0% {
    width: 0;
    right: 46px;
    top: 54px;
  }
  65% {
    width: 0;
    right: 46px;
    top: 54px;
  }
  84% {
    width: 55px;
    right: 0px;
    top: 35px;
  }
  100% {
    width: 47px;
    right: 8px;
    top: 38px;
  }
}
</style>