<template>
  <div class="modal fade me-2" id="showSuggestedUser" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="showSuggestedUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-sm-scrollable">
      <div class="modal-content ">
        <div class="modal-body bg-info-subtle border-0">
          <div class="p-3 border-bottom">
            <h5 class="mb-3">New Message</h5>
            <div class="input-group">
              <span class="input-group-text bg-white border-end-0">
                <i class="bi bi-search"></i>
              </span>
              <input type="text" v-model="userSearchQuery" class="form-control border-start-0 ps-0" placeholder="Search users...">
            </div>
          </div>
          <div class="flex-grow-1 overflow-auto">
            <div class="p-3 border-bottom d-flex justify-content-center">
              <small class="text-muted font-monospace fs-5 fw-semibold">SUGGESTED</small>
            </div>
            <div 
              v-for="suggestedUser in filteredUsers" 
              :key="suggestedUser.id"
              class="p-3 border border-top-0 d-flex align-items-center justify-content-between hover-bg"
              role="button"
            >
              <div class="d-flex align-items-center">
                <i class="bi bi-person-circle text-dark me-3" style="font-size: 40px;"></i>
                <div>
                  <h6 class="mb-0">{{ suggestedUser.first_name }}</h6>
                  <small class="text-muted">{{ formatRole(suggestedUser.role) }}</small>
                </div>
              </div>
              <button 
                class="btn btn-sm btn-primary"
                :disabled="isLoading"
                @click="handleMessageClick(suggestedUser)"
              >
                <span v-if="!isLoading">Message</span>
                <span v-else><i class="bi bi-hourglass-split"></i></span>
              </button>
            </div>
          </div>
          <div class="d-flex d-flex-row justify-content-end align-items-center mb-1 mt-3">
            <button class="btn btn-danger" type="button"  data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { Modal } from 'bootstrap'
  import { ref, computed} from 'vue';
  const props = defineProps({
    users: Array,
    startConversation: Function,
  });

  const formatRole = (role) => {
    if (!role) return 'User'; // default fallback
    return role
      .split('_') // split words by underscore
      .map(word => word.charAt(0).toUpperCase() + word.slice(1)) // capitalize each
      .join(' '); // join them back with spaces
  };
  const userSearchQuery = ref('');

  const filteredUsers = computed(() => {
    if (!userSearchQuery.value) return props.users;
    
    return props.users.filter(user => 
      user.first_name?.toLowerCase().includes(userSearchQuery.value.toLowerCase())
    );
  });

  const isLoading = ref(false);

const handleMessageClick = async (user) => {
  isLoading.value = true;

  try {
    const modalElement = document.getElementById('showSuggestedUser');
    const modal = Modal.getInstance(modalElement);
    modal.hide();

    await props.startConversation(user);
  } finally {
    isLoading.value = false;
  }
};

</script>