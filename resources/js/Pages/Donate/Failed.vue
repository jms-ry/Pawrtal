<template>
  <Head title="Payment Failed" />
  <AppLayout>
    <div class="container mt-5 mb-5">
      <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
          <div class="card border-0 shadow-sm">
            <div class="card-body text-center p-5">
              
              <!-- Failed Icon -->
              <div class="mb-4">
                <div class="failed-icon">
                  <div class="cross-circle">
                    <span class="line line-left"></span>
                    <span class="line line-right"></span>
                  </div>
                </div>
              </div>

              <!-- Failed Message -->
              <h2 class="text-danger fw-bold mb-3">Payment Failed</h2>
              <p class="text-muted mb-4">
                Unfortunately, your donation could not be processed.
                This may be due to cancellation, expiration, or a network issue.
              </p>

              <p class="small text-muted mb-4">
                No charges were made. You may try again anytime.
              </p>

              <!-- Action Buttons -->
              <div class="d-grid gap-2">
                <a href="/donate" class="btn btn-danger btn-lg">
                  <i class="bi bi-arrow-repeat me-2"></i>
                  Try Again
                </a>
                <a href="/" class="btn btn-outline-secondary">
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
</script>

<style scoped>
  .failed-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto;
  }

  /* Circle Animation */
  .cross-circle {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    border: 4px solid #dc3545;
    position: relative;
    box-sizing: content-box;
    transform: scale(0);
    animation: popIn 0.4s ease-out forwards;
  }

  /* Lines */
  .line {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 40px;
    height: 5px;
    background-color: #dc3545;
    border-radius: 2px;
    transform-origin: center;
    opacity: 0;
  }

  .line-left {
    transform: translate(-50%, -50%) rotate(45deg) scaleX(0);
    animation: drawLine 0.3s ease-out 0.4s forwards;
  }

  .line-right {
    transform: translate(-50%, -50%) rotate(-45deg) scaleX(0);
    animation: drawLine 0.3s ease-out 0.55s forwards;
  }

  /* Keyframes */

  @keyframes popIn {
    0% {
      transform: scale(0);
    }
    70% {
      transform: scale(1.1);
    }
    100% {
      transform: scale(1);
    }
  }

  @keyframes drawLine {
    0% {
      opacity: 0;
      transform: translate(-50%, -50%) rotate(45deg) scaleX(0);
    }
    100% {
      opacity: 1;
      transform: translate(-50%, -50%) rotate(45deg) scaleX(1);
    }
  }

  /* Fix for second line rotation */
  .line-right {
    animation-name: drawLineRight;
  }

  @keyframes drawLineRight {
    0% {
      opacity: 0;
      transform: translate(-50%, -50%) rotate(-45deg) scaleX(0);
    }
    100% {
      opacity: 1;
      transform: translate(-50%, -50%) rotate(-45deg) scaleX(1);
    }
  }
</style>

