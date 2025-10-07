<template>
  <div class="container-fluid mx-auto shadow-lg p-3 mb-5 rounded-4">
    <div v-if="(!notifications || notifications.data.length === 0) && !hasActiveFilters" class="d-flex flex-column align-items-center justify-content-center my-5">
      <i class="bi bi-exclamation-circle fs-1 text-muted mb-2"></i>
      <p class="fs-4 fw-semibold text-muted">No notifications yet.</p>
    </div>
    <!-- No results message -->
    <div v-else-if="notifications.data.length === 0 && hasActiveFilters" class="text-center py-5">
      <div class="mb-4">
        <i class="bi bi-search display-1 text-muted"></i>
      </div>
      <h4 class="text-muted mb-3">No notifications found</h4>
      <p class="text-muted">
        <span v-if="hasActiveFilters">
          Try adjusting your search criteria or clearing some filters.
        </span>
        <span v-else>
          There are currently no notifications available.
        </span>
      </p>
    </div>
    <div v-else>
      <!-- Mark All as Read Button -->
      <div class="d-flex justify-content-end mb-3">
        <button class="btn btn-warning fw-semibold">
          <i class="bi bi-envelope-open me-1"></i> Mark All as Read
        </button>
      </div>

      <!--Large Screen Table-->
      <div class="d-none d-md-block">
        <table class="table table-striped table-hover align-middle text-center">
          <thead class="table-primary">
            <tr>
              <th scope="col" style="width: 5%;">#</th>
              <th scope="col" style="width: 50%;">Message</th>
              <th scope="col" style="width: 20%;">Time Received</th>
              <th scope="col" style="width: 25%;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(notification, index) in notifications.data" :key="notification.id">
              <th scope="row">{{ index + 1 }}</th>
              <td>{{ notification.data.message }}</td>
              <td>{{ timeAgo(notification.created_at) }}</td>
              <td>
                <div class="d-flex justify-content-center align-items-center">
                  <a class="btn btn-success fw-bolder me-1">
                    <i class="bi bi-eye me-1"></i> View Notification
                  </a>
                  <a v-if="!notification.read_at" class="btn btn-warning fw-bolder me-1">
                    <i class="bi bi-envelope-open me-1"></i> Mark as Read 
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
              <th scope="col">Message</th>
              <th scope="col">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(notification, index) in notifications.data" :key="notification.id">
              <th scope="row">{{ index + 1 }}</th>
              <td>{{ notification.data.message }}</td>
              <td>
                <a class="btn btn-success btn-sm fw-bolder mb-1 w-100"
                  ><i class="bi bi-eye me-1"></i> View
                </a>
                <a v-if="!notification.read_at" class="btn btn-warning btn-sm fw-bolder mb-1 w-100"
                  ><i class="bi bi-envelope-open me-1"></i> Mark
                </a>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <!-- Pagination (only show if there are results) -->
      <div v-if="notifications.data.length > 0">
        <!--Large Screen Navigation-->
        <div class="d-none d-md-flex justify-content-between align-items-center mt-md-5">
          <div class="text-dark">
            <span v-if="notifications.from === notifications.to">
              <strong>Showing {{ notifications.from }} of {{ notifications.total }} notifications</strong>
            </span>
            <span v-else>
              <strong>Showing {{ notifications.from || 0 }} to {{ notifications.to || 0 }} of {{ notifications.total }} notifications</strong>
            </span>
          </div>
          <div class="btn-group" role="group" aria-label="Pagination">
            <button type="button" class="btn btn-info" :disabled="!notifications.prev_page_url" @click="goToPage(notifications.current_page - 1)">
              <span class="align-items-center">
                <i class="bi bi-chevron-double-left"></i> 
                Prev
              </span>
            </button>
            <button type="button" class="btn btn-info" :disabled="!notifications.next_page_url" @click="goToPage(notifications.current_page + 1)">
              <span class="align-items-center">Next 
                <i class="bi bi-chevron-double-right"></i>
              </span>
            </button>
          </div>
        </div>
        
        <!--Small Screen Navigation-->
        <div class="d-md-none d-flex flex-column align-items-center mt-3">
          <div class="text-dark">
            <span v-if="notifications.from === notifications.to">
              <strong>Showing {{ notifications.from || 0 }} of {{ notifications.total }} {{ notifications.total === 1 ? 'rescue' : 'notifications' }}</strong>
            </span>
            <span v-else>
              <strong>Showing {{ notifications.from || 0 }} to {{ notifications.to || 0 }} of {{ notifications.total }} notifications</strong>
            </span>
          </div>
          <div class="btn-group mt-3 w-100" role="group" aria-label="Pagination">
            <button type="button" class="btn btn-info" :disabled="!notifications.prev_page_url" @click="goToPage(notifications.current_page - 1)">
              <span class="align-items-center">
                <i class="bi bi-chevron-double-left"></i> 
                Prev
              </span>
            </button>
            <button type="button" class="btn btn-info" :disabled="!notifications.next_page_url" @click="goToPage(notifications.current_page + 1)">
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

  const props = defineProps({
    notifications: {
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
  const timeAgo = (dateString) => {
    const date = new Date(dateString);
    const seconds = Math.floor((Date.now() - date.getTime()) / 1000);
    const intervals = {
      year: 31536000,
      month: 2592000,
      week: 604800,
      day: 86400,
      hour: 3600,
      minute: 60,
    };

    for (const [unit, value] of Object.entries(intervals)) {
      const count = Math.floor(seconds / value);
      if (count >= 1) {
        return new Intl.RelativeTimeFormat('en', { numeric: 'auto' }).format(-count, unit);
      }
    }
    return 'just now';
  };

  const hasActiveFilters = computed(() => {
    return !!(props.filters.search || props.filters.read_at || props.filters.sort);
  });
  const goToPage = (page) => {
    if(page < 1 || page > props.notifications.last_page){
      return;
    }
    const params = page === 1 ? {} : { page };
  
    // Preserve all active filters when navigating pages
    if (props.filters.search) {
      params.search = props.filters.search;
    }
  
    if (props.filters.read_at) {
      params.read_at = props.filters.read_at;
    }

    if (props.filters.sort) {
      params.sort = props.filters.sort;
    }

    router.get(`/users/my-notifications`,params,{
      preserveState:true,
      preserveScroll:true,
    })
  };
</script>
