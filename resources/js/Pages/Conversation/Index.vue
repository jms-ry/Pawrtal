<template>
  <Head title="Conversation" />
  <AppLayout>
    <div class="container-fluid">
      <div class="card border-0 p-md-5">
        <ConversationHeader
          :previousUrl="previousUrl"
        />

        <ConversationDisplay
          :users="users"
          :conversations="conversations"
          :user="user"
        />
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
  import { Head } from '@inertiajs/vue3'
  import AppLayout from '../../Layouts/AppLayout.vue';
  import ConversationHeader from '../../Components/Conversation/ConversationHeader.vue';
  import ConversationDisplay from '../../Components/Conversation/ConversationDisplay.vue';

  const props = defineProps({
    previousUrl:String,
    users: Array,
    conversations: Array,
    user:Object
  })
</script>