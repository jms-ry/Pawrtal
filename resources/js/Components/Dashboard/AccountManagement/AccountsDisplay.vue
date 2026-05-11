<template>
  <div class="container-fluid mx-auto shadow-lg p-3 mb-5 rounded-4">
    <div v-if="(!users || users.data.length === 0) && !hasActiveFilters" class="d-flex flex-column align-items-center justify-content-center my-5">
      <i class="bi bi-exclamation-circle fs-1 text-muted mb-2"></i>
      <p class="fs-4 fw-semibold text-muted">No accounts yet.</p>
    </div>
    <!-- No results message -->
    <div v-else-if="users.data.length === 0 && hasActiveFilters" class="text-center py-5">
      <div class="mb-4">
        <i class="bi bi-search display-1 text-muted"></i>
      </div>
      <h4 class="text-muted mb-3">No staff accounts found</h4>
      <p class="text-muted">
        <span v-if="hasActiveFilters">
          Try adjusting your search criteria or clearing some filters.
        </span>
        <span v-else>
          There are currently no staff accounts available.
        </span>
      </p>
    </div>
    <div v-else>
      <!--Large Screen Table-->
      <div class="d-none d-md-block">
        <table class="table table-striped table-hover align-middle text-center">
          <thead class="table-primary">
            <tr>
              <th scope="col">#</th>
              <th scope="col">First Name</th>
              <th scope="col">Last Name</th>
              <th scope="col">Email</th>
              <th scope="col">Contact Number</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(user, index) in users.data" :key="user.id">
              <th scope="row">{{ index + 1 }}</th>
              <td>{{ user.first_name }}</td>
              <td>{{ user.last_name }}</td>
              <td>{{ user.email }}</td>
              <td>{{ user.contact_number }}</td>
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
              <th scope="col">First Name</th>
              <th scope="col">Last Name</th>
              <th scope="col">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(user, index) in users.data" :key="user.id">
              <th scope="row">{{ index + 1 }}</th>
              <td>{{ user.first_name }}</td>
              <td>{{ user.last_name }}</td>
              <td>
                <a class="btn btn-success fw-bolder mb-1 w-100" data-bs-toggle="modal" data-bs-target="#viewUserModal"
                  :data-user-first-name="user.first_name"
                  :data-user-last-name="user.last_name"
                  :data-user-email="user.email"
                  :data-user-contact-number="user.contact_number"
                  >View
                </a>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <ViewAccountModal/>
      <!-- Pagination (only show if there are results) -->
      <div v-if="users.data.length > 0">
        <!--Large Screen Navigation-->
        <div class="d-none d-md-flex justify-content-between align-items-center mt-md-5">
          <div class="text-dark">
            <span v-if="users.from === users.to">
              <strong>Showing {{ users.from }} of {{ users.total }} reports</strong>
            </span>
            <span v-else>
              <strong>Showing {{ users.from || 0 }} to {{ users.to || 0 }} of {{ users.total }} users</strong>
            </span>
          </div>
          <div class="btn-group" role="group" aria-label="Pagination">
            <button type="button" class="btn btn-info" :disabled="!users.prev_page_url" @click="goToPage(users.current_page - 1)">
              <span class="align-items-center">
                <i class="bi bi-chevron-double-left"></i> 
                Prev
              </span>
            </button>
            <button type="button" class="btn btn-info" :disabled="!users.next_page_url" @click="goToPage(users.current_page + 1)">
              <span class="align-items-center">Next 
                <i class="bi bi-chevron-double-right"></i>
              </span>
            </button>
          </div>
        </div>
        
        <!--Small Screen Navigation-->
        <div class="d-md-none d-flex flex-column align-items-center mt-3">
          <div class="text-dark">
            <span v-if="users.from === users.to">
              <strong>Showing {{ users.from || 0 }} of {{ users.total }} {{ users.total === 1 ? 'rescue' : 'users' }}</strong>
            </span>
            <span v-else>
              <strong>Showing {{ users.from || 0 }} to {{ users.to || 0 }} of {{ users.total }} users</strong>
            </span>
          </div>
          <div class="btn-group mt-3 w-100" role="group" aria-label="Pagination">
            <button type="button" class="btn btn-info" :disabled="!users.prev_page_url" @click="goToPage(users.current_page - 1)">
              <span class="align-items-center">
                <i class="bi bi-chevron-double-left"></i> 
                Prev
              </span>
            </button>
            <button type="button" class="btn btn-info" :disabled="!users.next_page_url" @click="goToPage(users.current_page + 1)">
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
  import ViewAccountModal from '../../Modals/ViewAccountModal.vue';

  const props = defineProps({
    users: {
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
    if(page < 1 || page > props.users.last_page){
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

    router.get(`/dashboard/account-management`,params,{
      preserveState:true,
      preserveScroll:false,
    })
  };
</script>