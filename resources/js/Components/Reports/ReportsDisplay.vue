<template>
  <div class="container-fluid mx-auto shadow-lg p-3 mb-5 rounded-4">
    <div v-if="!reports || reports.length === 0" class="d-flex flex-column align-items-center justify-content-center my-5">
      <i class="bi bi-exclamation-circle fs-1 text-muted mb-2"></i>
      <p class="fs-4 fw-semibold text-muted">No reports yet.</p>
      <a href="" class="btn btn-primary mt-2 fw-semibold">Create your first report</a>
    </div>
    <div v-else class="g-4 row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 justify-content-center" data-controller="view-report-modal delete-modal update-lost-report update-found-report">
      <div v-for="report in reports" :key="report.id" class="col-12 col-md-3 rounded-4 border-primary-subtle bg-warning-subtle mx-2 px-1 mt-4 mt-md-5" data-aos="zoom-in-up" data-aos-delay="200">
        <div class="card border-0 bg-warning-subtle h-100">
          <div class="card-header bg-warning-subtle border-0 d-flex justify-content-center mt-2">
            <span class="text-dark text-uppercase fs-4 ms-2 p-2 fw-lighter">{{ report.type_formatted }}</span>
          </div>
          <div class="card-body d-flex flex-column">
            <div class="ratio ratio-4x3 p-0 p-md-2 mt-0 rescue-card">
              <img :src="report.image_url" alt="Gallery image" class="w-100 h-100 object-fit-cover rounded-4">
            </div>
            <div class="d-flex flex-column mt-md-3 mt-2">
              <div class="d-flex flex-column" v-if="report.is_lost_report">
                <span class="ms-3 mt-3"><strong>Name: </strong>{{ report.animal_name_formatted }}</span>
                <span class="ms-3 mt-3"><strong>Last Seen at: </strong>{{ report.found_last_seen_location_formatted }}</span>
                <span class="ms-3 mt-3"><strong>Last Seen on: </strong>{{ report.found_last_seen_date }}</span>
              </div>
              <div class="d-flex flex-column" v-else>
                <span class="ms-3 mt-3"><strong>Found at: </strong>{{ report.found_last_seen_location_formatted }}</span> 
                <span class="ms-3 mt-3"><strong>Found on: </strong>{{ report.found_last_seen_date }}</span>
              </div>
              <span class="ms-3 mt-3"><strong>Sex: </strong>{{ report.sex_formatted }}</span>
              <span class="ms-3 mt-3"><strong>Date Reported:</strong> {{ report.reported_date}}</span>
              <span class="ms-3 mt-3"><strong>Status:</strong> {{ report.status_label}}</span>
            </div>
          </div>
          <div class="card-footer bg-warning-subtle border-0 d-flex gap-2 justify-content-between px-3 mt-auto mx-auto mb-2">
            <div v-if="report.is_still_active" class="d-flex justify-content-between">
              <a data-bs-toggle="modal" data-bs-target="#viewReportModal" class="btn btn-light me-1"
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
                :data-report-status="report.report_status"
                :data-report-status-label="report.status_label"
                :data-report-owned-by-logged-user="report.owned_by_logged_user ? 'true': 'false'"
                :data-report-logged-user-is-admin-or-staff="report.logged_user_is_admin_or_staff ? 'true' : 'false'"
                >View Report 
              </a>
              <div v-if="report.owned_by_logged_user" >
                <a class="btn btn-primary fw-bolder ms-1" data-bs-toggle="modal" 
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
                  >Update Report
                </a>
              </div>
              <div v-else>
                <a class="btn btn-info fw-bolder ms-1">Alert Owner</a> 
              </div>
            </div>
            <div v-else class="d-flex">
              <a data-bs-toggle="modal" data-bs-target="#viewReportModal" class="btn btn-light me-1"
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
                :data-report-status="report.report_status"
                :data-report-status-label="report.status_label"
                :data-report-owned-by-logged-user="report.owned_by_logged_user ? 'true': 'false'"
                :data-report-logged-user-is-admin-or-staff="report.logged_user_is_admin_or_staff ? 'true' : 'false'"
                >View Report 
              </a>
              <div v-if="report.owned_by_logged_user" >
                <a class="btn btn-primary fw-bolder ms-1" data-bs-toggle="modal" 
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
                  >Update Report
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
      <ViewReportModal />
      
      <UpdateLostReportModal 
        :user="user"
      />
      <UpdateFoundReportModal
        :user="user"
      />
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
  import UpdateFoundReportModal from '../Modals/Reports/UpdateFoundReportModal.vue';
  import UpdateLostReportModal from '../Modals/Reports/UpdateLostReportModal.vue';
  import ViewReportModal from '../Modals/Reports/ViewReportModal.vue';
  
  const props = defineProps({
    reports: {
      type: Array,
      default: () => []
    },
    user: {
      type: Object,
      default: () => null
    }
  })
</script>