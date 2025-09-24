<template>
  <div class="container px-md-5">
    <hr class="mt-0 mb-4">
    <div class="bg-warning-subtle p-md-3 mt-3 mt-md-0 rounded-3">
      <div class="row g-3">
        <div class="col-12 col-md-6 mt-md-5 py-md-5 px-2 px-md-0">
          <div class="card border-0 rounded-4 overflow-hidden m-md-5 m-2 mt-md-5" style="height: 350px;">
            <img :src="rescue.profile_image_url" :alt="rescue.name" class="w-100 h-100 object-fit-cover rounded-4">
          </div>
        </div>
        <div class="col-12 col-md-6 mt-md-5">
          <div class="card border-0 mt-md-5 bg-warning-subtle ">
            <div class="flex flex-row mb-md-3 mx-md-5 mx-2">
              <div class="align-items-center bg-secondary p-md-2 mb-2 rounded-4">
                <h3 class="text-center fw-bold display-6 font-monospace text-uppercase">{{ rescue.name }}</h3>
              </div>
              <div class="bg-secondary p-4 p-md-2 mt-3 rounded-4">
                <div class="flex flex-row align-items-start ms-md-3 ms-1 mt-2 mt-md-3 me-1 me-md-3 mb-3">
                  <p class="fs-5 lead font-monospace">
                    <span class="fw-bolder me-2">Gender:</span>
                    <span> {{ rescue.sex_formatted }}</span>
                  </p>
                  <p class="fs-5 lead font-monospace">
                    <span class="fw-bolder me-2">Age:</span>
                    <span> {{ rescue.age_formatted }}</span>
                  </p>
                  <p class="fs-5 lead font-monospace">
                    <span class="fw-bolder me-2">Color:</span>
                    <span> {{ rescue.color_formatted }}</span>
                  </p>
                  <p class="fs-5 lead font-monospace">
                    <span class="fw-bolder me-2">Description:</span>
                    <span> {{ rescue.description_formatted }}</span>
                  </p>
                  <p class="fs-5 lead font-monospace">
                    <span class="fw-bolder me-2">Size:</span>
                    <span> {{ rescue.size_formatted }}</span>
                  </p>
                  <p class="fs-5 lead font-monospace">
                    <span class="fw-bolder me-2">Distinctive Features:</span>
                    <span> {{ rescue.distinctive_features_formatted }}</span>
                  </p>
                  <p class="fs-5 lead font-monospace">
                    <span class="fw-bolder me-2">Vaccination Status:</span>
                    <span> {{ rescue.vaccination_status_formatted }}</span>
                  </p>
                  <p class="fs-5 lead font-monospace">
                    <span class="fw-bolder me-2">Spayed/Neutered:</span>
                    <span> {{ rescue.spayed_neutered_formatted }}</span>
                  </p>
                  <p v-show="rescue.is_available" class="fs-5 lead font-monospace fst-italic mt-4">
                    <span> {{ rescue.adoption_applications_count_formatted }}</span>
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="d-md-flex d-flex-row justify-content-center mt-4 mt-md-2 mb-md-2" data-controller="profile-reminder adoption-application">
        <ProfileReminder
          :user = "user"
        />
        <LoginReminder />
        <AdoptionApplicationForm />

        <div v-if="rescue.is_available">
          <div v-if="user?.isNonAdminOrStaff || !user" class="d-flex justify-content-center me-1">
            <div class="d-flex justify-content-center me-1">
              <a class="btn btn-lg btn-primary fw-bold mt-4 mt-md-0 mb-2 mb-md-0" data-bs-toggle="modal"
                :data-user-id="user?.id"
                :data-adoptable-name="rescue.name"
                :data-adoptable-id="rescue.id"
                :data-bs-target="!user ? '#loginReminderModal' : (user.canAdopt ? '#adoptionApplicationFormModal' : '#profileReminderModal')">
                Adopt Me!
              </a>
            </div>
          </div>
        </div>
        <div v-else-if="rescue.is_adopted">
          <div v-if="user?.isNonAdminOrStaff || !user" class="d-flex justify-content-center mb-4 mt-2">
            <p class="badge text-bg-primary fs-6 fw-lighter mt-4 mt-md-0 mb-2 mb-md-0 font-monospace fst-italic text-center p-md-2 py-2 px-md-2"><i class="bi bi-house-check-fill"></i> I'm already adopted!</p>
          </div>
        </div>
        <div v-else>
          <div v-if="user?.isNonAdminOrStaff || !user" class="d-flex justify-content-center mb-4 mt-2">
            <p class="badge text-bg-danger fs-6 fw-lighter mt-4 mt-md-0 mb-2 mb-md-0 font-monospace fst-italic text-center p-md-2 py-2 px-md-2"><i class="bi bi-exclamation-triangle-fill"></i> I'm not yet available for adoption!</p>
          </div>
        </div>
        <div v-if="user?.isAdminOrStaff" class="d-flex flex-row">
          <div class="d-flex justify-content-center me-1">
            <a class="btn btn-lg btn-info fw-bold mt-0 mb-2 mb-md-2" data-bs-toggle="modal" data-bs-target="#editRescueProfileModal"
              :data-rescue-profile-image="rescue.profile_image_url"
              :data-rescue-name="rescue.name"
              >Update Rescue Profile
            </a>
          </div>
          <div class="d-flex justify-content-center me-1">
            <a class="btn btn-lg btn-danger fw-bold mt-0 mb-2 mb-md-2" data-bs-toggle="modal" data-bs-target="#deleteRescueProfileModal" :class="{'disabled' : rescue.adoption_applications_count > 0}">Delete Rescue Profile</a>
          </div>
        </div>
      </div>
      <DeleteRescueProfileModal 
        :rescue="rescue"
      />

      <UpdateRescueProfileModal
        :rescue="rescue"
      />
    </div>
  </div>
</template>

<script setup>

  const props = defineProps({
    rescue:{
      type: Object,
      required: true
    },
    user: {
      type: Object,
      default: null,
    },
  });

  import ProfileReminder from '@/Components/Modals/ProfileReminder.vue';
  import LoginReminder from '@/Components/Modals/LoginReminder.vue';
  import AdoptionApplicationForm from '@/Components/Modals/Adoption/AdoptionApplicationForm.vue';
  import DeleteRescueProfileModal from '../../Modals/Rescues/DeleteRescueProfileModal.vue';
  import UpdateRescueProfileModal from '../../Modals/Rescues/UpdateRescueProfileModal.vue';
</script>