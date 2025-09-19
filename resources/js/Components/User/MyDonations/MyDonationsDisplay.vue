<template>
  <div class="container-fluid mx-auto shadow-lg p-3 mb-5 rounded-4">
    <div v-if="(!donations || donations.data.length === 0) && !hasActiveFilters" class="d-flex flex-column align-items-center justify-content-center my-5">
      <i class="bi bi-exclamation-circle fs-1 text-muted mb-2"></i>
      <p class="fs-4 fw-semibold text-muted">No donations yet.</p>
      <a :href="`/donate`" class="btn btn-primary mt-2 fw-semibold">Create your first donation!</a>
    </div>
    <!-- No results message -->
    <div v-else-if="donations.data.length === 0 && hasActiveFilters" class="text-center py-5">
      <div class="mb-4">
        <i class="bi bi-search display-1 text-muted"></i>
      </div>
      <h4 class="text-muted mb-3">No donations found</h4>
      <p class="text-muted">
        <span v-if="hasActiveFilters">
          Try adjusting your search criteria or clearing some filters.
        </span>
        <span v-else>
          There are currently no donations available.
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
              <th scope="col">Type</th>
              <th scope="col">Amount</th>
              <th scope="col">Item Description</th>
              <th scope="col">Item Quantity</th>
              <th scope="col">Donation Date</th>
              <th scope="col">Status</th>
              <th scope="col">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(donation, index) in donations.data" :key="donation.id">
              <th scope="row">{{ index + 1 }}</th>
              <td>{{ donation.donation_type_formatted }}</td>
              <td>{{ donation.amount_formatted }}</td>
              <td>{{ donation.item_description_formatted }}</td>
              <td>{{ donation.item_quantity_formatted }}</td>
              <td>{{ donation.donation_date_formatted }}</td>
              <td>{{ donation.status_label }}</td>
              <td>
                <div class="d-flex justify-content-center align-items-center">
                  <a class="btn btn-success fw-bolder me-1" data-bs-toggle="modal" data-bs-target="#viewDonationModal"
                    :data-donation-id="donation.id"
                    :data-donation-type-formatted="donation.donation_type_formatted"
                    :data-donation-item-description="donation.item_description_formatted"
                    :data-donation-item-quantity="donation.item_quantity_formatted"
                    :data-donation-pick-up-location="donation.pick_up_location_formatted"
                    :data-donation-contact-person="donation.contact_person_formatted"
                    :data-donation-status="donation.status"
                    :data-donation-type="donation.donation_type"
                    :data-donation-image="donation.donation_image_url"
                  >View </a>
                  <a v-if="donation.status === 'pending'" class="btn btn-info fw-bolder ms-1" data-bs-toggle="modal" >Update </a>
                  <a v-else class="btn btn-light fw-bolder ms-1" data-bs-toggle="modal" data-bs-target="#archiveDonationModal" :data-donation-id="donation.id">Archive </a>
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
            <tr v-for="(donation, index) in donations.data" :key="donation.id">
              <th scope="row">{{ index + 1 }}</th>
              <td>{{ donation.donation_type_formatted }}</td>
              <td>{{ donation.status_label }}</td>
              <td>
                <a class="btn btn-success fw-bolder me-1 mb-2 w-100" data-bs-toggle="modal" data-bs-target="#viewDonationModal"
                  :data-donation-id="donation.id"
                  :data-donation-type-formatted="donation.donation_type_formatted"
                  :data-donation-item-description="donation.item_description_formatted"
                  :data-donation-item-quantity="donation.item_quantity_formatted"
                  :data-donation-pick-up-location="donation.pick_up_location_formatted"
                  :data-donation-contact-person="donation.contact_person_formatted"
                  :data-donation-status="donation.status"
                  :data-donation-type="donation.donation_type"
                  :data-donation-image="donation.donation_image_url"
                >View </a>
                <a v-if="donation.status === 'pending'" class="btn btn-info fw-bolder mb-1 w-100">Update</a>
                <a v-else class="btn btn-light fw-bolder ms-1 w-100" data-bs-toggle="modal" data-bs-target="#archiveDonationModal" :data-donation-id="donation.id" >Archive </a>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <ViewDonationModal/>
      <ArchiveDonationModal/>
      <!-- Pagination (only show if there are results) -->
      <div v-if="donations.data.length > 0">
        <!--Large Screen Navigation-->
        <div class="d-none d-md-flex justify-content-between align-items-center mt-md-5">
          <div class="text-dark">
            <span v-if="donations.from === donations.to">
              <strong>Showing {{ donations.from }} of {{ donations.total }} donations</strong>
            </span>
            <span v-else>
              <strong>Showing {{ donations.from || 0 }} to {{ donations.to || 0 }} of {{ donations.total }} donations</strong>
            </span>
          </div>
          <div class="btn-group" role="group" aria-label="Pagination">
            <button type="button" class="btn btn-info" :disabled="!donations.prev_page_url" @click="goToPage(donations.current_page - 1)">
              <span class="align-items-center">
                <i class="bi bi-chevron-double-left"></i> 
                Prev
              </span>
            </button>
            <button type="button" class="btn btn-info" :disabled="!donations.next_page_url" @click="goToPage(donations.current_page + 1)">
              <span class="align-items-center">Next 
                <i class="bi bi-chevron-double-right"></i>
              </span>
            </button>
          </div>
        </div>
        
        <!--Small Screen Navigation-->
        <div class="d-md-none d-flex flex-column align-items-center mt-3">
          <div class="text-dark">
            <span v-if="donations.from === donations.to">
              <strong>Showing {{ donations.from || 0 }} of {{ donations.total }} {{ donations.total === 1 ? 'rescue' : 'donations' }}</strong>
            </span>
            <span v-else>
              <strong>Showing {{ donations.from || 0 }} to {{ donations.to || 0 }} of {{ donations.total }} donations</strong>
            </span>
          </div>
          <div class="btn-group mt-3 w-100" role="group" aria-label="Pagination">
            <button type="button" class="btn btn-info" :disabled="!donations.prev_page_url" @click="goToPage(donations.current_page - 1)">
              <span class="align-items-center">
                <i class="bi bi-chevron-double-left"></i> 
                Prev
              </span>
            </button>
            <button type="button" class="btn btn-info" :disabled="!donations.next_page_url" @click="goToPage(donations.current_page + 1)">
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
  import ViewDonationModal from '../../Modals/Users/MyDonations/ViewDonationModal.vue';
  import ArchiveDonationModal from '../../Modals/Users/MyDonations/ArchiveDonationModal.vue';

  const props = defineProps({
    donations: {
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
    return !!(props.filters.search || props.filters.donation_type || props.filters.status || props.filters.sort);
  });
  const goToPage = (page) => {
    if(page < 1 || page > props.donations.last_page){
      return;
    }
    const params = page === 1 ? {} : { page };
  
    // Preserve all active filters when navigating pages
    if (props.filters.search) {
      params.search = props.filters.search;
    }
  
    if (props.filters.type) {
      params.type = props.filters.donation_type;
    }
  
    if (props.filters.status) {
      params.status = props.filters.status;
    }

    if (props.filters.sort) {
      params.sort = props.filters.sort;
    }

    router.get(`/users/my-donations`,params,{
      preserveState:true,
      preserveScroll:true,
    })
  };
</script>