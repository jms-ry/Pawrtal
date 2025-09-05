<template>
  <Head title="Donate"></Head>
  
  <div class="card mt-4 mt-md-3 mb-4 mb-md-2 border-0 me-2 me-md-5 ms-2 ms-md-5 px-1 px-md-5">
    <LoginReminder />
    <CreateInKIndDonation
      :user="user"
    />
    <div class="card-body border-0 p-2 p-md-5 mx-auto">
       <div class="card-header border-0 bg-secondary">
          <h3 class="text-center fw-bolder display-6 font-monospace mb-4">Donate</h3>
          <h4 class="text-center fw-ligher fs-3 mt-4 font-monospace">Make a difference with a monetary or in-kind donation today!</h4>
      </div>
      <div class="container-fluid mx-auto shadow-lg p-3 mb-5 rounded-4 mt-4">
        <div class="row g-5 mt-1 justify-content-center mb-5">
          <div class="col-12 col-md-4">
            <div class="card bg-success border-0 h-100">
              <div class="card-body d-flex flex-column text-center">
                <p class="fs-2 fw-bold font-monospace mt-3 mb-3 p-0">Monetary Donation</p>
                <p class="lead mt-2 mt-md-3 fs-6 px-2 px-md-5 lh-lg"style="text-align: justify;">Every donation, big or small, makes a world of difference. Your support helps us feed hungry rescues, fund lifesaving medical treatments, and give them the love and care they truly deserve. Join us in turning sorrowful pasts into hopeful futures. With your kindness, we can save more lives and bring them closer to loving forever homes.</p>
                <a :href="`/donations/create`" class="btn btn-warning btn-lg fw-bolder w-50 rounded-pill px-3 mt-auto mx-auto">Donate</a>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-4">
            <div class="card bg-success border-0 h-100">
              <div class="card-body d-flex flex-column text-center">
                <p class="fs-2 fw-bold font-monospace mt-3 mb-3 p-0">In-kind Donation</p>
                <p class="lead mt-2 mt-md-3 fs-6 px-2 px-md-5 lh-lg"style="text-align: justify;">In-kind donations keep our shelter running every day. Your gifts—whether it’s pet food, cleaning supplies, or medicine—help us care for animals who’ve been through so much. These essentials make their days healthier and happier. Your kindness brings hope and healing to our rescues. Be part of their journey from hardship to happiness.</p>
                <a class="btn btn-warning btn-lg fw-bolder w-50 rounded-pill px-3 mt-auto mx-auto" data-bs-toggle="modal" :data-bs-target="!user ? '#loginReminderModal' : '#createInKindDonationModal'" >Donate</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { Head } from '@inertiajs/vue3';
  import LoginReminder from '@/Components/Modals/LoginReminder.vue';
import CreateInKIndDonation from '../../Components/Modals/Donate/CreateInKIndDonation.vue';

  const props = defineProps({
    user: {
      type: Object,
      default: () => null
    }
  });
</script>