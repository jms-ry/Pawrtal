<template>
  <div class="container-fluid mx-auto shadow-lg p-0 mb-5 border rounded-4 overflow-hidden" style="max-width: 95%; height: 700px;">
    <div class="row h-100 g-0">
      
      <!-- Left Panel: Conversations List -->
      <div class="col-12 col-md-4 col-lg-3 border-end bg-light h-100 d-flex flex-column" :class="{ 'd-none': selectedConversation && isMobile }">
        <!-- Header -->
        <div class="p-3 border-bottom bg-white">
          <h5 class="mb-3">Chats</h5>
          <div class="input-group">
            <input 
              type="text" 
              v-model="searchQuery"
              class="form-control ms-1 ps-0" 
              placeholder="  Search conversations..."
            >
            <span class="input-group-text bg-white">
              <i class="bi bi-search"></i>
            </span>
          </div>
        </div>

        <!-- Conversations List -->
        <div class="flex-grow-1 overflow-auto">
          <template v-if="filteredConversations.length">
            <div 
              v-for="conversation in filteredConversations" 
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
                    {{ getOtherParticipant(conversation)?.first_name || 'Unknown User' }}
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
      <div class="col-12 col-md-8 col-lg-6 h-100 d-flex flex-column " :class="{ 'd-none': !selectedConversation && isMobile }">
        <template v-if="selectedConversation">
          <!-- Chat Header -->
          <div class="p-3 border-bottom bg-white d-flex align-items-center">
            <a v-if="isMobile" class="text-decoration-none text-dark p-0 me-5" @click="selectedConversation = null">
              <i class="bi bi-arrow-left fs-4"></i>
            </a>
            <div class="me-3">
              <i class="bi bi-person-circle text-dark" style="font-size: 45px;"></i>
            </div>
            <div>
              <h6 class="mb-0">{{ getOtherParticipant(selectedConversation)?.first_name }}</h6>
              <small class="text-muted">{{ formatRole(getOtherParticipant(selectedConversation)?.role) }}</small>
            </div>
          </div>

          <!-- Messages Area -->
          <div ref="messageContainer" class="flex-grow-1 overflow-auto p-3 bg-light">
            <div v-if="selectedConversation.messages?.length === 0" class="text-center text-muted py-5">
              <i class="bi bi-chat-text mb-3 d-block" style="font-size: 2rem;"></i>
              <p>Start a conversation!</p>
            </div>
            <div 
              v-for="message in selectedConversation.messages" 
              :key="message.id"
              class="d-flex mb-3"
              :class="{ 'justify-content-end': message.sender_id === user.id }"
            >
              <div v-if="message.sender_id !== user.id" class="me-2">
                <i class="bi bi-person-circle text-dark" style="font-size: 32px;"></i>
              </div>
              <div class="message-bubble" :class="message.sender_id === user.id ? 'sent' : 'delivered'">
                <p class="mb-1">{{ message.content }}</p>
                <div class="d-flex align-items-center justify-content-end gap-1">
                  <small class="text-muted" style="font-size: 0.7rem;">{{ formatTime(message.created_at) }}</small>
                  <span v-if="message.sender_id === user.id">
                    <i v-if="message.status === 'sent'" class="bi bi-check text-muted" style="font-size: 0.9rem;"></i>
                    <i v-else-if="message.status === 'delivered'" class="bi bi-check-circle-fill text-muted" style="font-size: 0.9rem;"></i>
                    <i v-else-if="message.status === 'read'" class="bi bi-check-all text-dark" style="font-size: 0.9rem;"></i>
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Message Input -->
          <div class="p-3 border-top bg-white">
            <form @submit.prevent="sendMessage" class="input-group rounded-4 shadow-sm overflow-hidden">
              <button class="btn btn-light rounded-start-4 px-4" type="button" title="Attach file">
                <i class="bi bi-paperclip"></i>
              </button>
              <input 
                v-model="newMessage"
                type="text" 
                class="form-control px-3" 
                placeholder="Type a message..."
                :disabled="isSending"
              >
              <button 
                class="btn btn-success rounded-end-4 px-4" 
                type="submit"
                :disabled="!newMessage.trim() || isSending"
              >
                <span v-if="isSending" class="spinner-border spinner-border-sm" role="status"></span>
                <i v-else class="bi bi-send-fill"></i>
              </button>
            </form>
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
            <input 
              v-model="userSearchQuery"
              type="text" 
              class="form-control ps-0" 
              placeholder="  Search users..."
            >
            <span class="input-group-text bg-white">
              <i class="bi bi-search"></i>
            </span>
          </div>
        </div>

        <!-- Suggested Users -->
        <div class="flex-grow-1 overflow-auto">
          <div class="p-3 border-bottom bg-light">
            <small class="text-muted fw-semibold">SUGGESTED</small>
          </div>
          <div 
            v-for="suggestedUser in filteredUsers" 
            :key="suggestedUser.id"
            class="p-3 border-bottom d-flex align-items-center justify-content-between hover-bg"
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
              @click="startConversation(suggestedUser)"
              :disabled="isCreatingConversation"
            >
              <span v-if="isCreatingConversation" class="spinner-border spinner-border-sm me-1"></span>
              Message
            </button>
          </div>
        </div>
      </div>

      <!-- Mobile: New Message Button -->
      <button 
        v-if="isMobile && !selectedConversation" 
        class="btn btn-success rounded-pill position-fixed shadow" 
        data-bs-toggle="modal" 
        data-bs-target="#showSuggestedUser" 
        style="bottom: 20px; right: 20px; width: 60px; height: 60px;"
      >
        <i class="bi bi-plus-lg fs-4"></i> 
      </button>

      <!-- Mobile User Selection Modal -->
      <ShowSuggestedUser
        :users="users"
        :startConversation="startConversation"
      />
    </div>
  </div>
</template>

<script setup>
  import { Modal } from 'bootstrap'
  import { ref, computed, onMounted, onUnmounted, nextTick, watch } from 'vue';
  import { router } from '@inertiajs/vue3';
  import ShowSuggestedUser from '../Modals/Conversations/ShowSuggestedUser.vue';
  import axios from 'axios';

  const props = defineProps({
    users: Array,
    conversations: Array,
    user: Object
  });

  const windowWidth = ref(window.innerWidth);
  const selectedConversation = ref(null);
  const newMessage = ref('');
  const isSending = ref(false);
  const isCreatingConversation = ref(false);
  const searchQuery = ref('');
  const userSearchQuery = ref('');
  const messageContainer = ref(null);
  const localConversations = ref([...props.conversations]);

  const isMobile = computed(() => windowWidth.value < 768);

  const filteredConversations = computed(() => {
    if (!searchQuery.value) return localConversations.value;
    
    return localConversations.value.filter(conversation => {
      const otherUser = getOtherParticipant(conversation);
      return otherUser?.first_name?.toLowerCase().includes(searchQuery.value.toLowerCase());
    });
  });

  const filteredUsers = computed(() => {
    if (!userSearchQuery.value) return props.users;
    
    return props.users.filter(user => 
      user.first_name?.toLowerCase().includes(userSearchQuery.value.toLowerCase())
    );
  });

  const getOtherParticipant = (conversation) => {
    if (!conversation) return null;
    return conversation.participant1_id === props.user.id 
      ? conversation.participant2 
      : conversation.participant1;
  };

  const selectConversation = (conversation) => {
    selectedConversation.value = conversation;
    // Mark messages as read when conversation is selected
    if (conversation.unread > 0) {
      markMessagesAsRead(conversation.id);
    }
    scrollToBottom();
  };

  const scrollToBottom = () => {
    nextTick(() => {
      if (messageContainer.value) {
        messageContainer.value.scrollTop = messageContainer.value.scrollHeight;
      }
    });
  };

  const startConversation = async (targetUser) => {
    isCreatingConversation.value = true;
    
    try {
      // Check if conversation already exists
      const existingConversation = localConversations.value.find(conv => {
        const otherParticipant = getOtherParticipant(conv);
        return otherParticipant?.id === targetUser.id;
      });

      if (existingConversation) {
        selectConversation(existingConversation);
        // Close modal if on mobile
        if (isMobile.value) {
          const modal = Modal.getInstance(document.getElementById('showSuggestedUser'));
          if (modal) modal.hide();
        }
        return;
      }

      // Create new conversation
      router.post('/conversations', {
        participant_id: targetUser.id
      }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: (page) => {
          // Find the newly created conversation
          const newConversation = page.props.conversations.find(conv => {
            const otherParticipant = getOtherParticipant(conv);
            return otherParticipant?.id === targetUser.id;
          });
          
          if (newConversation) {
            localConversations.value = page.props.conversations;
            selectConversation(newConversation);
            
            // Close modal if on mobile
            if (isMobile.value) {
              const modal = Modal.getInstance(document.getElementById('showSuggestedUser'));
              if (modal) modal.hide();
            }
          }
        }
      });
    } finally {
      isCreatingConversation.value = false;
    }
  };

  const sendMessage = async () => {
    if (!newMessage.value.trim() || !selectedConversation.value || isSending.value){
      return;
    } 
    
    isSending.value = true;
    const messageContent = newMessage.value;
    const conversationId = selectedConversation.value.id;
    
    // Optimistically add message to UI
    const tempMessage = {
      id: Date.now(),
      content: messageContent,
      sender_id: props.user.id,
      conversation_id: conversationId,
      status: 'sent',
      created_at: new Date().toISOString()
    };
    
    if (!selectedConversation.value.messages) {
      selectedConversation.value.messages = [];
    }
    selectedConversation.value.messages.push(tempMessage);
    newMessage.value = '';
    scrollToBottom();
    
    try {
      router.post(`/conversations/${conversationId}/messages`, {
        content: messageContent
      }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: (page) => {
          // Update conversation with fresh data
          const updatedConversation = page.props.conversations.find(c => c.id === conversationId);
          if (updatedConversation) {
            const index = localConversations.value.findIndex(c => c.id === conversationId);
            if (index !== -1) {
              localConversations.value[index] = updatedConversation;
              selectedConversation.value = updatedConversation;
            }
          }
          scrollToBottom();
        },
        onError: () => {
          // Remove optimistic message on error
          const messageIndex = selectedConversation.value.messages.findIndex(m => m.id === tempMessage.id);
          if (messageIndex !== -1) {
            selectedConversation.value.messages.splice(messageIndex, 1);
          }
          alert('Failed to send message. Please try again.');
        }
      });
    } finally {
      isSending.value = false;
    }
  };

  const markMessagesAsRead = async (conversationId) => {
    const conversation = localConversations.value.find(c => c.id === conversationId);
    if (!conversation) return;

    try {
      await axios.put(`/conversations/${conversationId}/read`);
      conversation.unread = 0;
      conversation.messages.forEach(msg => {
        if (msg.sender_id !== props.user.id) msg.status = 'read';
      });
      router.reload({ 
        preserveScroll: true,
      })
    } catch (error) {
      console.error(error);
    }
  };

  const formatRole = (role) => {
    if (!role) return 'User'; // default fallback
    return role
      .split('_') // split words by underscore
      .map(word => word.charAt(0).toUpperCase() + word.slice(1)) // capitalize each
      .join(' '); // join them back with spaces
  };

  const formatTime = (timestamp) => {
    if (!timestamp) return '';
    const date = new Date(timestamp);
    const now = new Date();
    
    // If today, show time
    if (date.toDateString() === now.toDateString()) {
      return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }
    
    // If this year, show date without year
    if (date.getFullYear() === now.getFullYear()) {
      return date.toLocaleDateString([], { month: 'short', day: 'numeric' });
    }
    
    // Otherwise show full date
    return date.toLocaleDateString();
  };

  const updateWindowWidth = () => {
    windowWidth.value = window.innerWidth;
  };

  // Watch for prop updates
  watch(() => props.conversations, (newConversations) => {
    localConversations.value = [...newConversations];
    
    // Update selected conversation if it exists in new data
    if (selectedConversation.value) {
      const updated = newConversations.find(c => c.id === selectedConversation.value.id);
      if (updated) {
        selectedConversation.value = updated;
      }
    }
  });

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
    border-left: 3px solid #15b5eb;
  }

  .message-bubble {
    max-width: 70%;
    padding: 0.75rem 1rem;
    border-radius: 1rem;
    word-wrap: break-word;
  }

  .message-bubble.sent {
    background-color: #68d391;
    color: rgb(10, 1, 1);
    border-bottom-right-radius: 0.25rem;
  }

  .message-bubble.delivered {
    background-color: #e3f2fd;
    color: #212529;
    border-bottom-left-radius: 0.25rem;
  }

  .hover-bg:hover {
    background-color: #f8f9fa;
    cursor: pointer;
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

  .spinner-border-sm {
    width: 1rem;
    height: 1rem;
    border-width: 0.2em;
  }
</style>