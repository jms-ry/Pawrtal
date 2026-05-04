<template>
  <div>
    <!-- About Us Section -->
  <div class="card mt-5 mt-md-4 border-0 align-items-center mx-auto">
    <div class="card-body p-3 p-md-5">
      <div class="container me-0 me-md-5 ms-0 ms-md-5">
        <h2 class="text-center font-monospace fw-bolder">About Us - Ormoc Stray Oasis</h2>
        <div class="container">
          <div class="row">
            <div class="col-12">
              <h3 class="fw-bold font-monospace text-start mt-5 lh-lg">Every life deserves a chance — and every stray deserves a home</h3>
              <p data-aos="fade-left" data-aos-duration="500" class="fs-5 mt-4 lh-lg" style="text-align: justify;">Ormoc Strays Oasis is a non-profit animal rescue and rehabilitation group based in Ormoc City, Leyte. Our mission is simple yet urgent: to rescue, rehabilitate, and rehome abandoned, neglected, and abused animals, while promoting responsible pet ownership within our community.</p>
              <p data-aos="fade-right" data-aos-duration="500" class="fs-5 lh-lg" style="text-align: justify;">What started as a small group of volunteers feeding strays on the streets has grown into a community-driven movement that gives second chances to hundreds of dogs and cats each year. Through compassion, dedication, and teamwork, we strive to be a safe haven — an oasis — for those who have been forgotten.</p>
              <p data-aos="fade-right" data-aos-duration="500" class="fs-5 lh-lg" style="text-align: justify;"><span class="me-2">🐾</span> Rescue & Rehabilitation – We respond to reports of injured, sick, or abandoned animals, providing medical care, food, and shelter.</p>
              <p data-aos="fade-right" data-aos-duration="500" class="fs-5 lh-lg" style="text-align: justify;"><span class="me-2">🐾</span> Adoption & Rehoming – We carefully screen adopters to ensure every rescued pet finds a loving and permanent home.</p>
              <p data-aos="fade-right" data-aos-duration="500" class="fs-5 lh-lg" style="text-align: justify;"><span class="me-2">🐾</span> Education & Awareness – We advocate for responsible pet ownership, vaccination, and spaying/neutering to reduce stray overpopulation.</p>
              <p data-aos="fade-right" data-aos-duration="500" class="fs-5 lh-lg" style="text-align: justify;"><span class="me-2">🐾</span> Community Outreach – We partner with schools, businesses, and local leaders to build a more compassionate Ormoc.</p>
            </div>
            <div class="col-12">
              <h3 class="fw-bold font-monospace text-start mt-5 lh-lg">Caring for your pets</h3>
              <p data-aos="fade-left" data-aos-duration="500" class="fs-5 mt-4 lh-lg" style="text-align: justify;">At Ormoc Strays Oasis, we believe that caring for pets goes beyond providing food and shelter — it’s about love, responsibility, and respect for life. Every animal depends on us for safety, comfort, and happiness.</p>
              <h4 class="fw-bold font-monospace text-start mt-4 lh-lg"><span class="me-2">❤️</span> Basic Care Tips</h4>
              <p data-aos="fade-right" data-aos-duration="500" class="fs-5 lh-lg" style="text-align: justify;"><span class="me-2">🐾</span> Feed a balanced diet – Provide clean water and nutritious food appropriate for your pet’s age and health.</p>
              <p data-aos="fade-right" data-aos-duration="500" class="fs-5 lh-lg" style="text-align: justify;"><span class="me-2">🐾</span> Keep vaccinations up to date – Protect your pets from preventable diseases.</p>
              <p data-aos="fade-right" data-aos-duration="500" class="fs-5 lh-lg" style="text-align: justify;"><span class="me-2">🐾</span> Regular vet check-ups – Early detection keeps your pets healthy and happy.</p>
              <p data-aos="fade-right" data-aos-duration="500" class="fs-5 lh-lg" style="text-align: justify;"><span class="me-2">🐾</span> Spay or neuter – Help reduce stray overpopulation and improve your pet’s well-being.</p>
              <p data-aos="fade-right" data-aos-duration="500" class="fs-5 lh-lg" style="text-align: justify;"><span class="me-2">🐾</span> Provide daily love and exercise – Playtime, walks, and affection build trust and joy.</p>
              <h4 class="fw-bold font-monospace text-start mt-4 lh-lg"><span class="me-2">🐶</span> Responsible Pet Ownership</h4>
              <p data-aos="fade-right" data-aos-duration="500" class="fs-5 lh-lg" style="text-align: justify;"><span class="me-2">🐾</span> Owning a pet is a lifelong commitment. They may not speak our language, but they understand kindness, patience, and care. Be their voice, protect them from harm, and make them part of your family.</p>
              <p data-aos="fade-right" data-aos-duration="500" class="fs-5 lh-lg" style="text-align: justify;"><span class="me-2">🐾</span> Together, let’s build a community where every pet is cared for, valued, and loved.</p>
            </div>
          </div>
        </div>
        
        <div class="d-flex flex-column justify-content-center align-items-center mt-5">
          <a :href="`/adoption`" class="btn btn-primary btn-lg fw-bolder w-md-75 px-3">ADOPT A RESCUE TODAY</a>
          <a data-bs-toggle="modal" data-bs-target="#adoptionTermsAndConditionsModal" class="text-decoration-underline mt-4 text-danger fw-bolder">Terms and Conditions for Adopting a Rescue</a>
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