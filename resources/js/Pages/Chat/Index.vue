<script setup>
import MainLayout from '@/Layouts/AuthenticatedLayout.vue'
defineOptions({ layout: MainLayout })

import { onMounted, ref } from 'vue'
import { usePage } from '@inertiajs/vue3'
import axios from 'axios'

// State
const messages = ref([])
const newMessage = ref('')
const { props } = usePage()
const user = props.auth?.user

// Initialize with messages passed from the controller
messages.value = props.messages || []

// Function to send a new message
const sendMessage = async () => {
  if (!newMessage.value.trim()) return

  try {
    await axios.post('/chat', {
      body: newMessage.value,
    })
    newMessage.value = ''
  } catch (e) {
    console.error('Message not sent', e)
  }
}

// Listen to broadcasted events
onMounted(() => {
  console.log('Subscribing to Echo...')
  window.Echo.channel('chat')
    .listen('MessageSent', (e) => {
      console.log('Received from Laravel:', e)
      messages.value.unshift(e.message)
    })
})

</script>

<template>
  <div class="max-w-3xl mx-auto p-6 space-y-4">
    <h1 class="text-2xl font-bold">Live Chat</h1>

    <div class="space-y-2 max-h-[400px] overflow-y-auto border p-4 rounded bg-gray-50">
      <div v-for="(msg, i) in messages" :key="i" class="p-2 border-b">
        <strong>{{ msg.user?.name }}:</strong> {{ msg.body }}
        <div class="text-xs text-gray-500">
          {{ new Date(msg.created_at).toLocaleString() }}
        </div>
      </div>
    </div>

    <div class="flex space-x-2">
      <input
        v-model="newMessage"
        @keyup.enter="sendMessage"
        type="text"
        class="w-full border rounded p-2"
        placeholder="Type your message..."
      />
      <button
        @click="sendMessage"
        class="bg-blue-600 text-white px-4 py-2 rounded"
      >
        Send
      </button>
    </div>
  </div>
</template>
