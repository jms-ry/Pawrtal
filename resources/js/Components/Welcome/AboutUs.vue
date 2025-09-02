<template>
  <div>
    <!-- About Us Section -->
  <div class="card mt-5 mt-md-4 border-0 align-items-center mx-auto">
    <div class="card-body p-3 p-md-5">
      <div class="container me-0 me-md-5 ms-0 ms-md-5">
        <h3 class="text-center font-monospace fw-bolder">About Us</h3>
        <p data-aos="fade-left" data-aos-duration="500" class="fs-5 mt-5 lh-lg" style="text-align: justify;">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
        <p data-aos="fade-right" data-aos-duration="500" class="fs-5 lh-lg" style="text-align: justify;">Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur?</p>
        <div class="d-flex flex-column justify-content-center align-items-center mt-5">
          <a href="{{ url('/adoption') }}" class="btn btn-primary btn-lg fw-bolder w-md-75 px-3">ADOPT A RESCUE TODAY</a>
          <a data-bs-toggle="modal" data-bs-target="#adoptionTermsAndConditionsModal" class="text-decoration-none mt-4 text-danger fw-bolder">Terms and Conditions for Adopting a Rescue</a>
        </div>
       <AdoptionTermsAndConditionsModal />
      </div>
    </div>
  </div>
  <!--End About Us Section-->
  
  </div>
</template>

<script setup>
  import AdoptionTermsAndConditionsModal from "@/Components/Modals/Welcome/AdoptionTermsAndConditionsModal.vue"
</script>