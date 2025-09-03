<template>
  <div class="container-fluid mx-auto shadow-lg p-3 mb-5 rounded-4" data-controller="profile-reminder adoption-application">
    <ProfileReminder
      :user = "user"
    />
    <LoginReminder />
    <AdoptionApplicationForm />
    <div class="g-4 row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 justify-content-center">
      <div v-for="rescue in rescues" :key="rescue.id" class="col-12 col-md-3 rounded-4 border-primary-subtle bg-warning-subtle mx-2 px-1 mt-4 mt-md-5" data-aos="zoom-in-up" data-aos-delay="200">
        <div class="my-2">
          <span class="text-dark fw-bolder text-uppercase fs-4 mb-3 ms-2 mt-5 p-2 font-monospace">{{ rescue.name }}</span>
          <span v-if="rescue.is_adopted" class="badge border-0 position-absolute top-0 end-0 m-2 px-2 py-2 bg-warning bg-opacity-75 text-dark fw-bold rounded"><i class="bi bi-heart-fill"></i></span>
        </div>

        <div class="p-2 mt-1 rescue-card border-0 rounded-4 overflow-hidden shadow-lg position-relative" style="height: 300px;">
          <img :src="rescue.profile_image_url" :alt="rescue.name" class="w-100 h-100 object-fit-cover rounded-4">
          <div class="position-absolute bottom-0 start-0 end-0 bg-warning-subtle bg-opacity-0 text-dark p-2 text-center">
            <strong>{{ rescue.tag_label }}</strong>
          </div>
        </div>

        <div class="row g-2 p-2 mt-1 mb-1">
          <div v-if="user?.isAdminOrStaff" class="col-12 text-center mx-auto">
            <a :href="`/rescues/${rescue.id}`" class="btn btn-light w-50">View Profile</a>
          </div>
          <div v-else >
            <div v-if="rescue.is_adopted || rescue.is_unavailable" class="col-12 text-center mx-auto">
              <a :href="`/rescues/${rescue.id}`" class="btn btn-light w-50">View Profile</a>
            </div>
            <div v-else class="col-12 text-center mx-auto d-flex gap-2 flex-row">
              <a :href="`/rescues/${rescue.id}`" class="btn btn-light w-100">View Profile</a>
              <a class="btn btn-primary w-100 fw-bolder" data-bs-toggle="modal"
                :data-user-id="user?.id"
                :data-adoptable-name="rescue.name"
                :data-adoptable-id="rescue.id"
                :data-bs-target="!user ? '#loginReminderModal' : (user.canAdopt ? '#adoptionApplicationFormModal' : '#profileReminderModal')">
                Adopt Me!
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="d-flex justify-content-end mt-4 mt-md-5">
      <div class="btn-group" role="group" aria-label="Basic example">
        <button type="button" class="btn btn-info"><span class="align-items-center"><i class="bi bi-chevron-double-left"></i> Prev</span></button>
        <button type="button" class="btn btn-info"><span class="align-items-center">Next <i class="bi bi-chevron-double-right"></i></span></button>
      </div>
    </div>
  </div>
</template>
<script setup>
  const props = defineProps({
    rescues: Array,
    user: {
      type: Object
    },
  });

  import ProfileReminder from '@/Components/Modals/ProfileReminder.vue';
  import LoginReminder from '@/Components/Modals/LoginReminder.vue';
  import AdoptionApplicationForm from '@/Components/Modals/Adoption/AdoptionApplicationForm.vue';
</script>