<template>
  <div class="container-fluid mx-auto shadow-lg p-3 mb-5 rounded-4">
    <div v-if="(!reports || reports.data.length === 0) && !hasActiveFilters" class="d-flex flex-column align-items-center justify-content-center my-5">
      <i class="bi bi-exclamation-circle fs-1 text-muted mb-2"></i>
      <p class="fs-4 fw-semibold text-muted">No reports yet.</p>
      <a :href="`/reports`" class="btn btn-primary mt-2 fw-semibold">Create your first report!</a>
    </div>
    <!-- No results message -->
    <div v-else-if="reports.data.length === 0 && hasActiveFilters" class="text-center py-5">
      <div class="mb-4">
        <i class="bi bi-search display-1 text-muted"></i>
      </div>
      <h4 class="text-muted mb-3">No reports found</h4>
      <p class="text-muted">
        <span v-if="hasActiveFilters">
          Try adjusting your search criteria or clearing some filters.
        </span>
        <span v-else>
          There are currently no reports available.
        </span>
      </p>
    </div>
    <div v-else data-controller="view-report-modal delete-modal update-lost-report update-found-report">
      <!--Large Screen Table-->
      <div class="d-none d-md-block">
        <table class="table table-striped table-hover align-middle text-center">
          <thead class="table-primary">
            <tr>
              <th scope="col">#</th>
              <th scope="col">Type</th>
              <th scope="col">Animal Name</th>
              <th scope="col">Location</th>
              <th scope="col">Date Reported</th>
              <th scope="col">Status</th>
              <th scope="col">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(report, index) in reports.data" :key="report.id">
              <th scope="row">{{ index + 1 }}</th>
              <td>{{ report.type_formatted }}</td>
              <td>{{ report.animal_name_formatted }}</td>
              <td>{{ report.found_last_seen_location_formatted }}</td>
              <td>{{ report.reported_date }}</td>
              <td>{{ report.status_label }}</td>
              <td>
                <div class="d-flex justify-content-center align-items-center">
                  <a data-bs-toggle="modal" data-bs-target="#viewReportModal" class="btn btn-success fw-bolder me-1"
                    :data-report-id="report.id"
                    :data-report-type="report.type"
                    :data-report-animal-name="report.animal_name_formatted"
                    :data-report-species="report.species"
                    :data-report-location="report.found_last_seen_location_formatted"
                    :data-report-seen-date="report.found_last_seen_date"
                    :data-report-breed="report.breed_formatted"
                    :data-report-color="report.color_formatted"
                    :data-report-sex="report.sex_formatted"
                    :data-report-age-estimate="report.age_estimate_formatted"
                    :data-report-size="report.size_formatted"
                    :data-report-distinctive-features="report.distinctive_features_formatted"
                    :data-report-condition="report.condition_formatted"
                    :data-report-temporary-shelter="report.temporary_shelter_formatted"
                    :data-report-owner-name="report.owner_full_name"
                    :data-report-owner-contact-number="report.owner_contact_number"
                    :data-report-owner-email="report.owner_email"
                    :data-report-status="report.status"
                    :data-report-status-label="report.status_label"
                    :data-report-owned-by-logged-user="report.owned_by_logged_user ? 'true': 'false'"
                    :data-report-logged-user-is-adminstaff="report.logged_user_is_admin_or_staff ? 'true' : 'false'"
                    :data-report-trashed="report.deleted_at ? 'true' : 'false'"
                    >View 
                  </a>
                  <a class="btn btn-info fw-bolder ms-1" data-bs-toggle="modal" 
                    :data-bs-target="report.type == 'lost' ? '#updateLostReportModal' :'#updateFoundReportModal'"
                    :data-report-id="report.id"
                    :data-report-status="report.status"
                    :data-report-animal-name="report.animal_name_formatted"
                    :data-report-species="report.species"
                    :data-report-location="report.found_last_seen_location_formatted"
                    :data-report-last-seen-date="report.last_seen_date"
                    :data-report-found-date="report.found_date"
                    :data-report-breed="report.breed"
                    :data-report-color="report.color_formatted"
                    :data-report-sex="report.sex"
                    :data-report-age-estimate="report.age_estimate_formatted"
                    :data-report-size="report.size"
                    :data-report-distinctive-features="report.distinctive_features_formatted"
                    :data-report-condition="report.condition_formatted"
                    :data-report-temporary-shelter="report.temporary_shelter_formatted"
                    :data-report-image="report.image_url"
                    :data-report-type-formatted="report.type_formatted"
                    >Update
                  </a>
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
              <th scope="col">Type</th>
              <th scope="col">Status</th>
              <th scope="col">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(report, index) in reports.data" :key="report.id">
              <th scope="row">{{ index + 1 }}</th>
              <td>{{ report.type_formatted }}</td>
              <td>{{ report.status_label }}</td>
              <td>
                <a data-bs-toggle="modal" data-bs-target="#viewReportModal" class="btn btn-success fw-bolder mb-1 w-100"
                  :data-report-id="report.id"
                  :data-report-type="report.type"
                  :data-report-animal-name="report.animal_name_formatted"
                  :data-report-species="report.species"
                  :data-report-location="report.found_last_seen_location_formatted"
                  :data-report-seen-date="report.found_last_seen_date"
                  :data-report-breed="report.breed_formatted"
                  :data-report-color="report.color_formatted"
                  :data-report-sex="report.sex_formatted"
                  :data-report-age-estimate="report.age_estimate_formatted"
                  :data-report-size="report.size_formatted"
                  :data-report-distinctive-features="report.distinctive_features_formatted"
                  :data-report-condition="report.condition_formatted"
                  :data-report-temporary-shelter="report.temporary_shelter_formatted"
                  :data-report-owner-name="report.owner_full_name"
                  :data-report-owner-contact-number="report.owner_contact_number"
                  :data-report-owner-email="report.owner_email"
                  :data-report-status="report.status"
                  :data-report-status-label="report.status_label"
                  :data-report-owned-by-logged-user="report.owned_by_logged_user ? 'true': 'false'"
                  :data-report-logged-user-is-admin-or-staff="report.logged_user_is_admin_or_staff ? 'true' : 'false'"
                  :data-report-trashed="report.deleted_at ? 'true' : 'false'"
                  >View 
                </a>
                <a class="btn btn-info fw-bolder mb-1 w-100" data-bs-toggle="modal" 
                  :data-bs-target="report.type == 'lost' ? '#updateLostReportModal' :'#updateFoundReportModal'"
                  :data-report-id="report.id"
                  :data-report-status="report.status"
                  :data-report-animal-name="report.animal_name_formatted"
                  :data-report-species="report.species"
                  :data-report-location="report.found_last_seen_location_formatted"
                  :data-report-last-seen-date="report.last_seen_date"
                  :data-report-found-date="report.found_date"
                  :data-report-breed="report.breed"
                  :data-report-color="report.color_formatted"
                  :data-report-sex="report.sex"
                  :data-report-age-estimate="report.age_estimate_formatted"
                  :data-report-size="report.size"
                  :data-report-distinctive-features="report.distinctive_features_formatted"
                  :data-report-condition="report.condition_formatted"
                  :data-report-temporary-shelter="report.temporary_shelter_formatted"
                  :data-report-image="report.image_url"
                  :data-report-type-formatted="report.type_formatted"
                  >Update
                </a>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <ViewReportModal/>
      <UpdateFoundReportModal
        :user="user"
      />
      <UpdateLostReportModal
        :user="user"
      />
        <!-- Pagination (only show if there are results) -->
      <div v-if="reports.data.length > 0">
        <!--Large Screen Navigation-->
        <div class="d-none d-md-flex justify-content-between align-items-center mt-md-5">
          <div class="text-dark">
            <span v-if="reports.from === reports.to">
              <strong>Showing {{ reports.from }} of {{ reports.total }} reports</strong>
            </span>
            <span v-else>
              <strong>Showing {{ reports.from || 0 }} to {{ reports.to || 0 }} of {{ reports.total }} reports</strong>
            </span>
          </div>
          <div class="btn-group" role="group" aria-label="Pagination">
            <button type="button" class="btn btn-info" :disabled="!reports.prev_page_url" @click="goToPage(reports.current_page - 1)">
              <span class="align-items-center">
                <i class="bi bi-chevron-double-left"></i> 
                Prev
              </span>
            </button>
            <button type="button" class="btn btn-info" :disabled="!reports.next_page_url" @click="goToPage(reports.current_page + 1)">
              <span class="align-items-center">Next 
                <i class="bi bi-chevron-double-right"></i>
              </span>
            </button>
          </div>
        </div>
        
        <!--Small Screen Navigation-->
        <div class="d-md-none d-flex flex-column align-items-center mt-3">
          <div class="text-dark">
            <span v-if="reports.from === reports.to">
              <strong>Showing {{ reports.from || 0 }} of {{ reports.total }} {{ reports.total === 1 ? 'rescue' : 'reports' }}</strong>
            </span>
            <span v-else>
              <strong>Showing {{ reports.from || 0 }} to {{ reports.to || 0 }} of {{ reports.total }} reports</strong>
            </span>
          </div>
          <div class="btn-group mt-3 w-100" role="group" aria-label="Pagination">
            <button type="button" class="btn btn-info" :disabled="!reports.prev_page_url" @click="goToPage(reports.current_page - 1)">
              <span class="align-items-center">
                <i class="bi bi-chevron-double-left"></i> 
                Prev
              </span>
            </button>
            <button type="button" class="btn btn-info" :disabled="!reports.next_page_url" @click="goToPage(reports.current_page + 1)">
              <span class="align-items-center">Next 
                <i class="bi bi-chevron-double-right"></i>
              </span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { router } from '@inertiajs/vue3';
  import { computed } from 'vue';
  import UpdateFoundReportModal from '../../Modals/Reports/UpdateFoundReportModal.vue';
  import UpdateLostReportModal from '../../Modals/Reports/UpdateLostReportModal.vue';
  import ViewReportModal from '../../Modals/Reports/ViewReportModal.vue';

  const props = defineProps({
    reports: {
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
    return !!(props.filters.search || props.filters.type || props.filters.status || props.filters.sort);
  });
  const goToPage = (page) => {
    if(page < 1 || page > props.reports.last_page){
      return;
    }
    const params = page === 1 ? {} : { page };
  
    // Preserve all active filters when navigating pages
    if (props.filters.search) {
      params.search = props.filters.search;
    }
  
    if (props.filters.type) {
      params.type = props.filters.type;
    }
  
    if (props.filters.status) {
      params.status = props.filters.status;
    }

    if (props.filters.sort) {
      params.sort = props.filters.sort;
    }

    router.get(`/users/my-reports`,params,{
      preserveState:true,
      preserveScroll:true,
    })
  };
</script>