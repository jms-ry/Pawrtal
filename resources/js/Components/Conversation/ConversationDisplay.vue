<template>
  <div class="container-fluid mx-auto shadow-lg p-0 mb-5 border rounded-4 overflow-hidden" style="max-width: 95%; height: 700px;">
    <div class="row h-100 g-0">
      
      <!-- Left Panel: Conversations List -->
      <div class="col-12 col-md-4 col-lg-3 border-end bg-light h-100 d-flex flex-column" :class="{ 'd-none': selectedConversation && isMobile }">
        <!-- Header -->
        <div class="p-3 border-bottom bg-white">
          <h5 class="mb-3">Chats</h5>
          <div class="input-group">
            <span class="input-group-text bg-white border-end-0">
              <i class="bi bi-search"></i>
            </span>
            <input type="text" class="form-control border-start-0 ps-0" placeholder="Search conversations...">
          </div>
        </div>

        <!-- Conversations List -->
        <div class="flex-grow-1 overflow-auto">
          <template v-if="props.conversations.length">
            <div 
              v-for="conversation in props.conversations" 
              :key="conversation.id"
              class="conversation-item p-3 border-bottom d-flex align-items-start position-relative"
              :class="{ 'active': selectedConversation?.id === conversation.id }"
              @click="selectConversation(conversation)"
              role="button"
            >
              <div class="me-3">
                <i class="bi bi-person-circle text-dark" style="font-size: 50px;"></i>
              </div>
              <div class="flex-grow-1 overflow-hidden">
                <div class="d-flex justify-content-between align-items-start mb-1">
                  <h6 class="mb-0 text-capitalize" :class="{ 'fw-bold': conversation.unread > 0 }">
                    {{ conversation.other_participant?.first_name || 'Unknown User' }}
                  </h6>
                  <small class="text-muted">{{ formatTime(conversation.last_message_at) }}</small>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                  <p class="mb-0 text-muted small text-truncate" :class="{ 'fw-semibold': conversation.unread > 0 }">
                    {{ conversation.messages?.[conversation.messages.length - 1]?.content || 'No messages yet' }}
                  </p>
                  <span v-if="conversation.unread > 0" class="badge bg-danger rounded-pill ms-2">{{ conversation.unread }}</span>
                </div>
              </div>
            </div>
          </template>

          <!-- Empty Conversations State -->
          <template v-else>
            <div class="d-flex flex-column align-items-center justify-content-center text-center py-5">
              <i class="bi bi-chat-dots text-muted mb-3" style="font-size: 3rem;"></i>
              <p class="text-muted mb-1 fw-semibold">No conversations yet</p>
              <small class="text-muted d-none d-md-block">Start a new message from the list on the right.</small>
              <small class="text-muted d-md-none d-block">Start a new message using the button below.</small>
            </div>
          </template>
        </div>
      </div>

      <!-- Middle Panel: Chat Area -->
      <div class="col-12 col-md-8 col-lg-6 h-100 d-flex flex-column" :class="{ 'd-none': !selectedConversation && isMobile }">
        <template v-if="selectedConversation">
          <!-- Chat Header -->
          <div class="p-3 border-bottom bg-white d-flex align-items-center">
            <a v-if="isMobile" class="text-decoration-none text-dark p-0 me-5" @click="selectedConversation = null"><i class="bi bi-arrow-left fs-4 "></i></a>
            <div class="me-3">
              <i class="bi bi-person-circle text-dark" style="font-size: 45px;"></i>
            </div>
            <div>
              <h6 class="mb-0">{{ selectedConversation.other_participant?.first_name }}</h6>
              <small class="text-muted">Last seen {{ selectedConversation.other_participant?.last_seen ?? 'recently' }}</small>
            </div>
          </div>

          <!-- Messages Area -->
          <div class="flex-grow-1 overflow-auto p-3 bg-light">
            <div 
              v-for="message in selectedConversation.messages" 
              :key="message.id"
              class="d-flex mb-3"
              :class="{ 'justify-content-end': message.sender_id === user.id }"
            >
              <div v-if="message.sender_id !== user.id" class="me-2">
                <i class="bi bi-person-circle text-dark" style="font-size: 32px;"></i>
              </div>
              <div class="message-bubble" :class="message.sender_id === user.id ? 'sent' : 'received'">
                <p class="mb-1">{{ message.content }}</p>
                <div class="d-flex align-items-center justify-content-end gap-1">
                  <small class="text-muted" style="font-size: 0.7rem;">{{ formatTime(message.created_at) }}</small>
                  <span v-if="message.sender_id === user.id">
                    <i v-if="message.status === 'sent'" class="bi bi-check text-muted" style="font-size: 0.9rem;"></i>
                    <i v-else-if="message.status === 'delivered'" class="bi bi-check-all text-muted" style="font-size: 0.9rem;"></i>
                    <i v-else-if="message.status === 'read'" class="bi bi-check-all text-dark" style="font-size: 0.9rem;"></i>
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Message Input -->
          <div class="p-3 border-top bg-white">
            <div class="input-group rounded-4 shadow-sm overflow-hidden">
              <button class="btn btn-light rounded-start-4 px-4" type="button" title="Attach file">
                <i class="bi bi-paperclip"></i>
              </button>
              <input type="text" class="form-control px-3" placeholder="Type a message...">
              <button class="btn btn-success rounded-end-4 px-4" type="button">
                <i class="bi bi-send-fill"></i>
              </button>
            </div>
          </div>
        </template>

        <!-- Empty State -->
        <div v-else class="h-100 d-flex align-items-center justify-content-center text-center p-4">
          <div>
            <i class="bi bi-chat-dots display-1 text-muted mb-3"></i>
            <h5 class="text-muted">Select a conversation to start messaging</h5>
          </div>
        </div>
      </div>

      <!-- Right Panel: New Message / User List -->
      <div class="col-12 col-lg-3 border-start bg-white h-100 d-none d-lg-flex flex-column">
        <!-- Header -->
        <div class="p-3 border-bottom">
          <h5 class="mb-3">New Message</h5>
          <div class="input-group">
            <span class="input-group-text bg-white border-end-0">
              <i class="bi bi-search"></i>
            </span>
            <input type="text" class="form-control border-start-0 ps-0" placeholder="Search users...">
          </div>
        </div>

        <!-- Suggested Users -->
        <div class="flex-grow-1 overflow-auto">
          <div class="p-3 border-bottom bg-light">
            <small class="text-muted fw-semibold">SUGGESTED</small>
          </div>
          <div 
            v-for="user in props.users" 
            :key="user.id"
            class="p-3 border-bottom d-flex align-items-center justify-content-between hover-bg"
            role="button"
          >
            <div class="d-flex align-items-center">
              <i class="bi bi-person-circle text-dark me-3" style="font-size: 40px;"></i>
              <div>
                <h6 class="mb-0">{{ user.first_name }}</h6>
                <small class="text-muted">{{ user.role }}</small>
              </div>
            </div>
            <button class="btn btn-sm btn-primary">Message</button>
          </div>
        </div>
      </div>

      <!-- Mobile: New Message Button -->
      <button v-if="isMobile" class="btn btn-success rounded-pill position-fixed shadow" data-bs-toggle="modal" data-bs-target="#showSuggestedUser" style="bottom: 20px; right: 20px; width: 60px; height: 60px;" >
        <i class="bi bi-plus-lg fs-4"></i> 
      </button>
      <ShowSuggestedUser
        :users="users"
      />
    </div>
  </div>
</template>

<script setup>
  import { ref, computed, onMounted, onUnmounted } from 'vue';
  import ShowSuggestedUser from '../Modals/Conversations/ShowSuggestedUser.vue';

  const props = defineProps({
    users: Array,
    conversations: Array,
    user: Object
  });

  const windowWidth = ref(window.innerWidth);
  const selectedConversation = ref(null);

  const isMobile = computed(() => windowWidth.value < 768);

  const selectConversation = (conversation) => {
    selectedConversation.value = conversation;
  };

  const formatTime = (timestamp) => {
    if (!timestamp) return '';
    const date = new Date(timestamp);
    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  };

  const updateWindowWidth = () => {
    windowWidth.value = window.innerWidth;
  };

  onMounted(() => {
    window.addEventListener('resize', updateWindowWidth);
  });

  onUnmounted(() => {
    window.removeEventListener('resize', updateWindowWidth);
  });
</script>

<style scoped>
  .conversation-item {
    cursor: pointer;
    transition: background-color 0.2s;
  }

  .conversation-item:hover {
    background-color: #f8f9fa;
  }

  .conversation-item.active {
    background-color: #68d391;
    border-left: 3px solid #2de215;
  }

  .message-bubble {
    max-width: 70%;
    padding: 0.75rem 1rem;
    border-radius: 1rem;
    word-wrap: break-word;
  }

  .message-bubble.sent {
    background-color: #68d391;
    color: rgb(19, 0, 0);
    border-bottom-right-radius: 0.25rem;
  }

  .message-bubble.received {
    background-color: #88baec;
    color: #212529;
    border-bottom-left-radius: 0.25rem;
  }

  .hover-bg:hover {
    background-color: #f8f9fa;
  }

  /* Scrollbar styling */
  .overflow-auto::-webkit-scrollbar {
    width: 6px;
  }

  .overflow-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
  }

  .overflow-auto::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
  }

  .overflow-auto::-webkit-scrollbar-thumb:hover {
    background: #555;
  }

  .empty-state {
    color: #6c757d;
  }
</style>
