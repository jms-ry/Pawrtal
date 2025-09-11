<template>
  <div>
    <!-- Large Screen -->
    <div class="card bg-primary border-0 d-none d-md-block">
      <div class="card-body p-0">
        <div class="carousel slide custom-carousel carousel-fade" id="welcomePetCarousel">
          <div class="carousel-inner">
            <div class="carousel-item active" style="height: 90vh;">
              <img src="/images/welcome/dog-3.jpg" alt="Happy pet" class="d-block w-100 h-100 object-fit-cover border-0" >
              <div class="position-absolute top-50 start-50 translate-middle text-center text-white">
                <h1 class="display-5 fw-bold">Saving lives, one paw at a time.</h1>
                <p class="lead fs-4">Together, we’re rewriting their stories.</p>
                <a href="" class="btn btn-primary btn-lg fw-bolder mt-2 btn-help-scroll">I WANT TO HELP</a>
              </div>
            </div>

            <div class="carousel-item" style="height: 90vh;">
              <img src="/images/welcome/dog-2.png" alt="Happy pet" class="d-block w-100 h-100 object-fit-cover border-0">
              <div class="position-absolute top-50 start-50 translate-middle text-center text-white">
                <h1 class="display-5 fw-bold">You can’t buy love, but you’ll find it in a rescue.</h1>
                <a :href="`/adoption`" class="btn btn-primary btn-lg fw-bolder mt-2">ADOPT FROM PAWRTAL</a>
              </div>
            </div>

            <div class="carousel-item" style="height: 90vh;">
              <img src="/images/welcome/dog-1.png" alt="Happy pet" class="d-block w-100 h-100 object-fit-cover border-0">
              <div class="position-absolute top-50 start-50 translate-middle text-center text-white">
                <h1 class="display-5 fw-bold">Lost a Pet? Found One?</h1>
                <p class="lead fs-4">Seen a wandering animal or lost your furry friend? Report it here so we can take action.</p>
                <a :href="`/reports`" class="btn btn-primary btn-lg fw-bolder mt-2">VIEW OR SUBMIT A REPORT</a>
              </div>
            </div>

            <div class="carousel-item" style="height: 90vh;">
              <img src="/images/welcome/dog-4.jpg" alt="Happy pet" class="d-block w-100 h-100 object-fit-cover border-0">
              <div class="position-absolute top-50 start-50 translate-middle text-center text-white">
                <h1 class="display-5 fw-bold">Every Life Matters — Let's Save One Today.</h1>
                <p class="lead fs-4">Even small acts of kindness can change lives — theirs and yours.</p>
                <a :href="`/donate`" class="btn btn-primary btn-lg fw-bolder mt-2">HELP US HELP THEM</a>
              </div>
            </div>
          </div>

          <button class="carousel-control-prev" type="button" data-bs-target="#welcomePetCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#welcomePetCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Small Screen -->
    <div class="card bg-primary border-0 d-lg-none">
      <div class="card-body p-0" style="height: 90vh;">
        <img src="/images/welcome/dog-3.jpg" alt="Happy pet"
             class="d-block w-100 h-100 object-fit-cover border-0">
        <div class="position-absolute top-50 start-50 translate-middle text-center text-white w-100 px-3">
          <h6 class="display-6 fw-bold">Saving lives, one paw at a time.</h6>
          <p class="lead fs-5">Together, we’re rewriting their stories.</p>
          <a href="" class="btn btn-primary btn-lg fw-bolder mt-2 btn-help-scroll">I WANT TO HELP</a>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  
</script>
