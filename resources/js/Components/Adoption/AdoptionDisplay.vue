<template>
  <div class="container-fluid mx-auto shadow-lg p-3 mb-5 rounded-4" data-controller="profile-reminder adoption-application">
    <ProfileReminder
      :user = "user"
    />
    <LoginReminder />
    <AdoptionApplicationForm />

    <!-- No results message -->
    <div v-if="adoptables.data.length === 0 && hasActiveFilters" class="text-center py-5">
      <div class="mb-4">
        <i class="bi bi-search display-1 text-muted"></i>
      </div>
      <h4 class="text-muted mb-3">No adoptables found</h4>
      <p class="text-muted">
        <span v-if="hasActiveFilters">
          Try adjusting your search criteria or clearing some filters.
        </span>
        <span v-else>
          There are currently no adoptables available.
        </span>
      </p>
    </div>


    <div v-if="(!adoptables || adoptables.data.length === 0) && !hasActiveFilters" class="d-flex flex-column align-items-center justify-content-center my-5">
      <i class="bi bi-exclamation-circle fs-1 text-muted mb-2"></i>
      <p class="fs-4 fw-semibold text-muted">No adoptable rescues yet.</p>
      <a v-if="user?.isAdminOrStaff" href="" class="btn btn-primary mt-2">Add an adoptable rescue</a>
      <p class="fs-4 fw-semibold text-muted">Check back later!</p>
    </div>

    <div v-else class="g-4 row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 justify-content-center">
      <div v-for="adoptable in adoptables.data" :key="adoptable.id" class="col-12 col-md-3 rounded-4 border-primary-subtle bg-warning-subtle mx-2 px-1 mt-4 mt-md-5" data-aos="zoom-in-up" data-aos-delay="200">
        <div class="my-2">
          <span class="text-dark fw-bolder text-uppercase fs-4 ms-2 mt-5 p-2 font-monospace">{{ adoptable.name }}</span>
        </div>
        <div class="p-2 rescue-card border-0 rounded-4 overflow-hidden shadow-lg position-relative" style="height: 300px;">
          <img :src="adoptable.profile_image_url" :alt="adoptable.name" class="w-100 h-100 object-fit-cover rounded-4">
          <div class="position-absolute bottom-0 start-0 end-0 bg-warning-subtle bg-opacity-0 text-dark p-2 text-center">
            <strong>{{ adoptable.tag_label }}</strong>
          </div>
        </div>
        <div class="row g-2 p-2 mt-1 mb-1">
          <div v-if="user?.isAdminOrStaff" class="col-12 text-center mx-auto">
            <a :href="`/rescues/${adoptable.id}`" class="btn btn-success w-50">View Profile</a>
          </div>
          <div v-else class="col-12 text-center mx-auto d-flex gap-2 flex-row">
            <a :href="`/rescues/${adoptable.id}`" class="btn btn-success w-100">View Profile</a>
            <a class="btn btn-primary w-100 fw-bolder" data-bs-toggle="modal"
              :data-user-id="user?.id"
              :data-adoptable-name="adoptable.name"
              :data-adoptable-id="adoptable.id"
              :data-bs-target="!user ? '#loginReminderModal' : (user.canAdopt ? '#adoptionApplicationFormModal' : '#profileReminderModal')"
              >Adopt Me
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Pagination (only show if there are results) -->
    <div v-if="adoptables.data.length > 0">
      <!--Large Screen Navigation-->
      <div class="d-none d-md-flex justify-content-between align-items-center mt-md-5">
        <div class="text-dark">
          <span v-if="adoptables.from === adoptables.to">
            <strong>Showing {{ adoptables.from }} of {{ adoptables.total }} adoptables</strong>
          </span>
          <span v-else>
            <strong>Showing {{ adoptables.from || 0 }} to {{ adoptables.to || 0 }} of {{ adoptables.total }} adoptables</strong>
          </span>
        </div>
        <div class="btn-group" role="group" aria-label="Pagination">
          <button type="button" class="btn btn-info" :disabled="!adoptables.prev_page_url" @click="goToPage(adoptables.current_page - 1)">
            <span class="align-items-center">
              <i class="bi bi-chevron-double-left"></i> 
              Prev
            </span>
          </button>
          <button type="button" class="btn btn-info" :disabled="!adoptables.next_page_url" @click="goToPage(adoptables.current_page + 1)">
            <span class="align-items-center">Next 
              <i class="bi bi-chevron-double-right"></i>
            </span>
          </button>
        </div>
      </div>
      
      <!--Small Screen Navigation-->
      <div class="d-md-none d-flex flex-column align-items-center mt-3">
        <div class="text-dark">
          <span v-if="adoptables.from === adoptables.to">
            <strong>Showing {{ adoptables.from || 0 }} of {{ adoptables.total }} {{ adoptables.total === 1 ? 'rescue' : 'adoptables' }}</strong>
          </span>
          <span v-else>
            <strong>Showing {{ adoptables.from || 0 }} to {{ adoptables.to || 0 }} of {{ adoptables.total }} adoptables</strong>
          </span>
        </div>
        <div class="btn-group mt-3 w-100" role="group" aria-label="Pagination">
          <button type="button" class="btn btn-info" :disabled="!adoptables.prev_page_url" @click="goToPage(adoptables.current_page - 1)">
            <span class="align-items-center">
              <i class="bi bi-chevron-double-left"></i> 
              Prev
            </span>
          </button>
          <button type="button" class="btn btn-info" :disabled="!adoptables.next_page_url" @click="goToPage(adoptables.current_page + 1)">
            <span class="align-items-center">Next 
              <i class="bi bi-chevron-double-right"></i>
            </span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { router } from '@inertiajs/vue3';
  import { computed } from 'vue';
  import ProfileReminder from '@/Components/Modals/ProfileReminder.vue';
  import LoginReminder from '@/Components/Modals/LoginReminder.vue';
  import AdoptionApplicationForm from '@/Components/Modals/Adoption/AdoptionApplicationForm.vue';
  const props = defineProps({
    adoptables: {
      type: Object,
      default: () => null
    },
    user: {
      type: Object,
      default: () => null
    },
    filters: {
      type: Object,
      default: () => ({})
    }
  });

  const hasActiveFilters = computed(() => {
    return !!(props.filters.search || props.filters.sex || props.filters.size);
  });
  const goToPage = (page) => {
    if(page < 1 || page > props.adoptables.last_page){
      return;
    }
    const params = page === 1 ? {} : { page };
  
    // Preserve all active filters when navigating pages
    if (props.filters.search) {
      params.search = props.filters.search;
    }
  
    if (props.filters.sex) {
      params.sex = props.filters.sex;
    }
  
    if (props.filters.size) {
      params.size = props.filters.size;
    }

    router.get(`/adoption`,params,{
      preserveState:true,
      preserveScroll:true,
    })
  };
</script>