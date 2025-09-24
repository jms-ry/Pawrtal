<template>
  <div class="container-fluid mx-auto shadow-lg p-3 mb-5 rounded-4">
    <!-- No results message -->
    <div v-if="rescues.data.length === 0" class="text-center py-5">
      <div class="mb-4">
        <i class="bi bi-search display-1 text-muted"></i>
      </div>
      <h4 class="text-muted mb-3">No rescues found</h4>
      <p class="text-muted">
        <span v-if="hasActiveFilters">
          Try adjusting your search criteria or clearing some filters.
        </span>
        <span v-else>
          There are currently no rescues available.
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
              <th scope="col">Name</th>
              <th scope="col">Breed</th>
              <th scope="col">Sex</th>
              <th scope="col">Age</th>
              <th scope="col">Adoption Status</th>
              <th scope="col">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(rescue, index) in rescues.data" :key="rescue.id">
              <th scope="row">{{ index + 1 }}</th>
              <td>{{ rescue.name_formatted }}</td>
              <td>{{ rescue.breed_formatted }}</td>
              <td>{{ rescue.sex_formatted }}</td>
              <td>{{ rescue.age_formatted }}</td>
              <td>{{ rescue.adoption_status_formatted }}</td>
              <td>
                <div class="d-flex justify-content-center align-items-center">
                  <a :href="`/rescues/${rescue.id}`" class="btn btn-success fw-bolder me-1">View Profile</a>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <!-- Pagination (only show if there are results) -->
    <div v-if="rescues.data.length > 0">
      <!--Large Screen Navigation-->
      <div class="d-none d-md-flex justify-content-between align-items-center mt-md-5">
        <div class="text-dark">
          <span v-if="rescues.from === rescues.to">
            <strong>Showing {{ rescues.from }} of {{ rescues.total }} rescues</strong>
          </span>
          <span v-else>
            <strong>Showing {{ rescues.from || 0 }} to {{ rescues.to || 0 }} of {{ rescues.total }} rescues</strong>
          </span>
        </div>
        <div class="btn-group" role="group" aria-label="Pagination">
          <button type="button" class="btn btn-info" :disabled="!rescues.prev_page_url" @click="goToPage(rescues.current_page - 1)">
            <span class="align-items-center">
              <i class="bi bi-chevron-double-left"></i> 
              Prev
            </span>
          </button>
          <button type="button" class="btn btn-info" :disabled="!rescues.next_page_url" @click="goToPage(rescues.current_page + 1)">
            <span class="align-items-center">Next 
              <i class="bi bi-chevron-double-right"></i>
            </span>
          </button>
        </div>
      </div>
      
      <!--Small Screen Navigation-->
      <div class="d-md-none d-flex flex-column align-items-center mt-3">
        <div class="text-dark">
          <span v-if="rescues.from === rescues.to">
            <strong>Showing {{ rescues.from || 0 }} of {{ rescues.total }} {{ rescues.total === 1 ? 'rescue' : 'rescues' }}</strong>
          </span>
          <span v-else>
            <strong>Showing {{ rescues.from || 0 }} to {{ rescues.to || 0 }} of {{ rescues.total }} rescues</strong>
          </span>
        </div>
        <div class="btn-group mt-3 w-100" role="group" aria-label="Pagination">
          <button type="button" class="btn btn-info" :disabled="!rescues.prev_page_url" @click="goToPage(rescues.current_page - 1)">
            <span class="align-items-center">
              <i class="bi bi-chevron-double-left"></i> 
              Prev
            </span>
          </button>
          <button type="button" class="btn btn-info" :disabled="!rescues.next_page_url" @click="goToPage(rescues.current_page + 1)">
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

  const props = defineProps({
    rescues: Object,
    filters: {
      type: Object,
      default: () => ({})
    }
  });

  const hasActiveFilters = computed(() => {
    return !!(props.filters.search || props.filters.sex || props.filters.size || props.filters.status);
  });

  const goToPage = (page) => {
    if (page < 1 || page > props.rescues.last_page) {
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
  
    if (props.filters.status) {
      params.status = props.filters.status;
    }

    router.get('/dashboard/rescues', params, {
      preserveState: false,
      preserveScroll: true,
    });
  };
</script>