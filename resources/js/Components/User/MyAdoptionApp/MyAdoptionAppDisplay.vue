<template>
  <div class="container-fluid mx-auto shadow-lg p-3 mb-5 rounded-4">
    <div v-if="(!adoptionApplications || adoptionApplications.data.length === 0) && !hasActiveFilters" class="d-flex flex-column align-items-center justify-content-center my-5">
      <i class="bi bi-exclamation-circle fs-1 text-muted mb-2"></i>
      <p class="fs-4 fw-semibold text-muted">No adoption applications yet.</p>
      <a :href="`/adoption`" class="btn btn-primary mt-2 fw-semibold">Create your first adoption applications!</a>
    </div>
    <!-- No results message -->
    <div v-else-if="adoptionApplications.data.length === 0 && hasActiveFilters" class="text-center py-5">
      <div class="mb-4">
        <i class="bi bi-search display-1 text-muted"></i>
      </div>
      <h4 class="text-muted mb-3">No adoption applications found</h4>
      <p class="text-muted">
        <span v-if="hasActiveFilters">
          Try adjusting your search criteria or clearing some filters.
        </span>
        <span v-else>
          There are currently no adoption applications available.
        </span>
      </p>
    </div>
    <div v-else>
      <!--Large Screen Table-->
      <div class="d-none d-md-block">
        <table class="table table-hover table-striped align-middle text-center">
          <thead class="table-primary">
            <tr>
              <th scope="col">#</th>
              <th scope="col">Rescue Name</th>
              <th scope="col">Application Date</th>
              <th scope="col">Status</th>
              <th scope="col">Archived?</th>
              <th scope="col">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(adoptionApplication, index) in adoptionApplications.data" :key="adoptionApplication.id">
              <th scope="row">{{ index + 1 }}</th>
              <td>{{ adoptionApplication.rescue_name_formatted }}</td>
              <td>{{ adoptionApplication.application_date_formatted }}</td>
              <td>{{ adoptionApplication.status_label }}</td>
              <td>{{ adoptionApplication.archived }}</td>
              <td>
                <div class="d-flex justify-content-center align-items-center">
                  <a class="btn btn-success fw-bolder me-1" data-bs-toggle="modal" data-bs-target="#viewApplicationModal"
                    :data-application-id="adoptionApplication.id"
                    :data-application-rescue-name="adoptionApplication.rescue_name_formatted"
                    :data-application-status="adoptionApplication.status"
                    :data-application-status-label="adoptionApplication.status_label"
                    :data-application-application-date="adoptionApplication.application_date_formatted"
                    :data-application-start-date="adoptionApplication.inspection_start_date_formatted"
                    :data-application-end-date="adoptionApplication.inspection_end_date_formatted"
                    :data-application-reason-for-adoption="adoptionApplication.reason_for_adoption_formatted"
                    :data-application-logged-user-is-admin-or-staff="adoptionApplication.logged_user_is_admin_or_staff"
                   >View 
                  </a>
                  <a v-if="adoptionApplication.status === 'pending'" class="btn btn-info fw-bolder ms-1" data-bs-toggle="modal" data-bs-target="#updateAdoptionApplicationFormModal"
                    :data-application-rescue-id="adoptionApplication.rescue.id"
                    :data-application-id="adoptionApplication.id"
                    :data-application-rescue-name="adoptionApplication.rescue_name_formatted"
                    :data-application-start-date="adoptionApplication.preferred_inspection_start_date"
                    :data-application-end-date="adoptionApplication.preferred_inspection_end_date"
                    :data-application-reason-for-adoption="adoptionApplication.reason_for_adoption_formatted"
                    >Update 
                  </a>
                  <a v-else-if="!adoptionApplication.deleted_at" class="btn btn-light fw-bolder ms-1" data-bs-toggle="modal" data-bs-target="#archiveApplicationModal" :data-application-id="adoptionApplication.id" >Archive </a>
                  <a v-else class="btn btn-info fw-bolder ms-1" data-bs-toggle="modal" data-bs-target="#restoreApplicationModal" :data-application-id="adoptionApplication.id" >Unarchive </a>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <!--Small Screen Table-->
      <div class="d-md-none d-block">
        <table class="table table-striped table-hover align-middle text-center">
          <thead class="table-primary">
            <tr>
              <th scope="col">#</th>
              <th scope="col">Rescue Name</th>
              <th scope="col">Status</th>
              <th scope="col">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(adoptionApplication, index) in adoptionApplications.data" :key="adoptionApplication.id">
              <th scope="row">{{ index + 1 }}</th>
              <td>{{ adoptionApplication.rescue_name_formatted }}</td>
              <td>{{ adoptionApplication.status_label }}</td>
              <td>
                <a class="btn btn-success fw-bolder mb-1 w-100" data-bs-toggle="modal" data-bs-target="#viewApplicationModal"
                  :data-application-id="adoptionApplication.id"
                  :data-application-rescue-name="adoptionApplication.rescue_name_formatted"
                  :data-application-status="adoptionApplication.status"
                  :data-application-status-label="adoptionApplication.status_label"
                  :data-application-application-date="adoptionApplication.application_date_formatted"
                  :data-application-start-date="adoptionApplication.inspection_start_date_formatted"
                  :data-application-end-date="adoptionApplication.inspection_end_date_formatted"
                  :data-application-reason-for-adoption="adoptionApplication.reason_for_adoption_formatted"
                  :data-application-logged-user-is-admin-or-staff="adoptionApplication.logged_user_is_admin_or_staff"
                  >View 
                </a>
                <a v-if="adoptionApplication.status === 'pending'" class="btn btn-info fw-bolder mb-1 w-100" data-bs-toggle="modal" data-bs-target="#updateAdoptionApplicationFormModal"
                  :data-application-rescue-id="adoptionApplication.rescue.id"
                  :data-application-id="adoptionApplication.id"
                  :data-application-rescue-name="adoptionApplication.rescue_name_formatted"
                  :data-application-start-date="adoptionApplication.preferred_inspection_start_date"
                  :data-application-end-date="adoptionApplication.preferred_inspection_end_date"
                  :data-application-reason-for-adoption="adoptionApplication.reason_for_adoption_formatted"
                  >Update
                </a>
                <a v-else-if="!adoptionApplication.deleted_at" class="btn btn-light fw-bolder mb-1 w-100" data-bs-toggle="modal" data-bs-target="#archiveApplicationModal" :data-application-id="adoptionApplication.id" >Archive </a>
                <a v-else class="btn btn-info fw-bolder mb-1 w-100" data-bs-toggle="modal" data-bs-target="#restoreApplicationModal" :data-application-id="adoptionApplication.id" >Unarchive </a>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <!-- Pagination (only show if there are results) -->
      <div v-if="adoptionApplications.data.length > 0">
        <!--Large Screen Navigation-->
        <div class="d-none d-md-flex justify-content-between align-items-center mt-md-5">
          <div class="text-dark">
            <span v-if="adoptionApplications.from === adoptionApplications.to">
              <strong>Showing {{ adoptionApplications.from }} of {{ adoptionApplications.total }} applications</strong>
            </span>
            <span v-else>
              <strong>Showing {{ adoptionApplications.from || 0 }} to {{ adoptionApplications.to || 0 }} of {{ adoptionApplications.total }} applications</strong>
            </span>
          </div>
          <div class="btn-group" role="group" aria-label="Pagination">
            <button type="button" class="btn btn-info" :disabled="!adoptionApplications.prev_page_url" @click="goToPage(adoptionApplications.current_page - 1)">
              <span class="align-items-center">
                <i class="bi bi-chevron-double-left"></i> 
                Prev
              </span>
            </button>
            <button type="button" class="btn btn-info" :disabled="!adoptionApplications.next_page_url" @click="goToPage(adoptionApplications.current_page + 1)">
              <span class="align-items-center">Next 
                <i class="bi bi-chevron-double-right"></i>
              </span>
            </button>
          </div>
        </div>
        
        <!--Small Screen Navigation-->
        <div class="d-md-none d-flex flex-column align-items-center mt-3">
          <div class="text-dark">
            <span v-if="adoptionApplications.from === adoptionApplications.to">
              <strong>Showing {{ adoptionApplications.from || 0 }} of {{ adoptionApplications.total }} {{ adoptionApplications.total === 1 ? 'rescue' : 'adoptionApplications' }}</strong>
            </span>
            <span v-else>
              <strong>Showing {{ adoptionApplications.from || 0 }} to {{ adoptionApplications.to || 0 }} of {{ adoptionApplications.total }} applications</strong>
            </span>
          </div>
          <div class="btn-group mt-3 w-100" role="group" aria-label="Pagination">
            <button type="button" class="btn btn-info" :disabled="!adoptionApplications.prev_page_url" @click="goToPage(adoptionApplications.current_page - 1)">
              <span class="align-items-center">
                <i class="bi bi-chevron-double-left"></i> 
                Prev
              </span>
            </button>
            <button type="button" class="btn btn-info" :disabled="!adoptionApplications.next_page_url" @click="goToPage(adoptionApplications.current_page + 1)">
              <span class="align-items-center">Next 
                <i class="bi bi-chevron-double-right"></i>
              </span>
            </button>
          </div>
        </div>
      </div>
    </div>
    <ViewApplicationModal
      :user="user"
    />
    <ArchiveApplicationModal/>
    <UpdateAdoptionApplicationForm 
      :user="user"
    />
    <UnarchiveApplicationModal/>
  </div>
</template>

<script setup>
  import { router } from '@inertiajs/vue3';
  import { computed } from 'vue';
  import ViewApplicationModal from '../../Modals/Users/MyAdoptionApplications/ViewApplicationModal.vue';
  import ArchiveApplicationModal from '../../Modals/Users/MyAdoptionApplications/ArchiveApplicationModal.vue';
  import UpdateAdoptionApplicationForm from '../../Modals/Adoption/UpdateAdoptionApplicationForm.vue';
  import UnarchiveApplicationModal from '../../Modals/Users/MyAdoptionApplications/UnarchiveApplicationModal.vue';

  const props = defineProps({
    adoptionApplications: {
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
  })

  const hasActiveFilters = computed(() => {
    return !!(props.filters.search || props.filters.status || props.filters.sort);
  });
  const goToPage = (page) => {
    if(page < 1 || page > props.adoptionApplications.last_page){
      return;
    }
    const params = page === 1 ? {} : { page };
  
    // Preserve all active filters when navigating pages
    if (props.filters.search) {
      params.search = props.filters.search;
    }
  
    if (props.filters.status) {
      params.status = props.filters.status;
    }

    if (props.filters.sort) {
      params.sort = props.filters.sort;
    }

    router.get(`/users/my-adoption-applications`,params,{
      preserveState:true,
      preserveScroll:true,
    })
  };
</script>