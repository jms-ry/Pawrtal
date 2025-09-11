<template>
  <!--How You Can Help Section-->
  <div id="how-you-can-help" class="card mt-5 mt-md-3 mb-4 mb-md-2 border-0">
    <div class="card-body border-0 p-3 p-md-5">
      <div class="container-fluid p-3 p-md-5">
        <h3 class="text-center fw-bolder display-6 font-monospace">How You Can Help</h3>
        <div class="row g-3 mt-5">
          <div class="col-12 col-md-4" data-aos="zoom-in-right" data-aos-delay="200">
            <div class="card bg-success border-0 h-100">
              <div class="card-body d-flex flex-column text-center">
                <p class="fs-2 fw-bold font-monospace mt-3 mb-3 p-0">Donate</p>
                <p class="lead mt-2 mt-md-3 fs-6 px-2 p-md-5 lh-lg"style="text-align: justify;">Every peso counts in saving a life. Your generous donations help us provide food, shelter, and medical care to hundreds of rescues who once had nothing. From basic needs to emergency surgeries, your support keeps their second chances possible. Be part of their journey toward healing and a forever home.</p>
                <a :href="`/donate`" class="btn btn-warning btn-lg fw-bolder w-50 rounded-pill px-3 mt-auto mx-auto">Donate</a>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-4" data-aos="zoom-in-up" data-aos-delay="400">
            <div class="card bg-success border-0 h-100">
              <div class="card-body d-flex flex-column text-center">
                <p class="fs-2 fw-bold font-monospace mt-3 mb-3 p-0">Adopt</p>
                <p class="lead mt-2 mt-md-3 fs-6 px-2 p-md-5 lh-lg"style="text-align: justify;">When you adopt, you don’t just save one life — you open space to save another. Each rescue carries a story of survival, hope, and quiet resilience. By giving them a home, you become the happy ending they’ve been waiting for. Find a loyal friend who will love you unconditionally.</p>
                <a :href="`/adoption`" class="btn btn-warning btn-lg fw-bolder w-50 rounded-pill px-3 mt-auto mx-auto">Adopt a Rescue</a>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-4" data-aos="zoom-in-left" data-aos-delay="600">
            <div class="card bg-success border-0 h-100">
              <div class="card-body d-flex flex-column text-center">
                <p class="fs-2 fw-bold font-monospace mt-3 mb-3 p-0">Report</p>
                <p class="lead mt-2 mt-md-3 fs-6 px-2 p-md-5 lh-lg"style="text-align: justify;">You could be the reason a lost pet finds its way home. Our Lost & Found service lets you report animals in need or reunite missing pets with their families. Whether you’ve found a stray or lost your furry companion, we’re here to help. Let’s work together to bring them back where they belong.</p>
                <a :href="`/reports`" class="btn btn-warning btn-lg fw-bolder w-50 rounded-pill px-3 mt-auto mx-auto">File a Report</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--End How You Can Help Section-->
</template>