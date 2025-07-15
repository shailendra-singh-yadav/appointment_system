<script setup>
import { ref, computed } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useToast } from 'vue-toastification'

const toast = useToast()

//Define props to get data from  controller
const props = defineProps({
  appointment: Object
})

// console.log('app:', props.appointment);

// Setup form
const form = useForm({
  title: props?.appointment?.title || '',
  description: props?.appointment?.description,
  date: props?.appointment?.date,
  guests: ['']
})

// Add/remove guest
const addGuest = () => form.guests.push('')
const removeGuest = (index) => form.guests.splice(index, 1)

// Title
const formTitle = props.appointment ? 'Edit' : 'Create'

// Validate weekdays only
function isWeekday(dateStr) {
  const date = new Date(dateStr)
  return !isNaN(date) && date.getDay() > 0 && date.getDay() < 6
}

// Submit handler
async function submit() {
  if (!isWeekday(form.date)) {
    toast.error('Please select a valid date (Monday to Friday only).')
    return
  }

  try {
    await form.post('/appointments', {
      preserveScroll: true,
    })
    toast.success('Appointment booked and invitations sent!')
  } catch (e) {
    if (Object.values(form.errors).length) {
      toast.error(Object.values(form.errors)[0])
    } else {
      toast.error('Something went wrong, please try again.')
    }
  }
}
</script>

<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-2xl font-semibold text-gray-800">{{ formTitle }} Appointment</h2>
    </template>

    <div class="max-w-3xl mx-auto p-6 space-y-6">
      <div class="bg-white shadow rounded-lg p-6">
        <!-- Title -->
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Title</label>
          <input v-model="form.title" type="text"
                 class="w-full border rounded px-3 py-2"
                 placeholder="Enter title"/>
          <p v-if="form.errors.title" class="text-red-600 text-sm">{{ form.errors.title }}</p>
        </div>

        <!-- Description -->
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Description</label>
          <textarea v-model="form.description" rows="3"
                    class="w-full border rounded px-3 py-2"
                    placeholder="Enter description"></textarea>
          <p v-if="form.errors.description" class="text-red-600 text-sm">{{ form.errors.description }}</p>
        </div>

        <!-- Date & Time -->
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Date & Time</label>
          <input v-model="form.date" type="datetime-local"
                 class="w-full border rounded px-3 py-2"/>
          <p v-if="form.errors.date" class="text-red-600 text-sm">{{ form.errors.date }}</p>
        </div>

        <!-- Guest Emails -->
        <div class="mb-6">
          <label class="block font-medium mb-2">Guest Emails</label>

          <div v-for="(email, index) in form.guests" :key="index"
               class="flex items-center space-x-2 mb-2">
            <input v-model="form.guests[index]" type="email" placeholder="guest@example.com"
                   class="flex-1 border rounded px-3 py-2"/>
            <button type="button" v-if="form.guests.length > 1"
                    @click="removeGuest(index)"
                    class="text-red-500 text-lg leading-none">×</button>
          </div>

          <button type="button" @click="addGuest" class="text-blue-600">
            + Add Guest
          </button>

          <p v-if="form.errors.guests" class="text-red-600 text-sm mt-1">
            {{ form.errors.guests }}
          </p>
        </div>

        <!-- Submit -->
        <div class="text-right">
          <button :disabled="form.processing"
                  @click="submit"
                  class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded disabled:opacity-50">
            Book Appointment
          </button>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
