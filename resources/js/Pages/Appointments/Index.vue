<script setup>
import MainLayout from '@/Layouts/AuthenticatedLayout.vue'
import { defineProps, onMounted, computed } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import { useToast } from 'vue-toastification'
import axios from 'axios'

const toast = useToast()
defineOptions({ layout: MainLayout })

const props = defineProps({
  appointments: Object,
  filters: Object,
  section: String, // Should be 'guests' or ''
})

const bookingId = props.filters?.bookingId ?? ''

const isGuestSection = computed(() => {
  return props.section === 'guest' || bookingId !== ''
})

const formatDateTime = (datetime) => {
  if (!datetime) return '-'
  const parsedDate = new Date(datetime)
  return isNaN(parsedDate)
    ? 'Invalid Date'
    : parsedDate.toLocaleString('en-IN', {
        dateStyle: 'medium',
        timeStyle: 'short',
      })
}

onMounted(() => {
  const flash = usePage().props.flash
  if (flash?.success) toast.success(flash.success)
  if (flash?.error) toast.error(flash.error)
})

// Fixed handleDelete function (uses correct appointment object)
function handleDelete(appointment) {
  console.log(appointment);
  if (!window.confirm('Are you sure you want to delete this appointment?')) return

  router.delete(`/appointments/${appointment.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Booking deleted successfully!')
    },
    onError: () => toast.error('Failed to delete appointment.'),
  })
}

// cancelAppointment
function cancelAppointment(id, bookingId) {
  if (!window.confirm('Are you sure you want to cancel this appointment?')) return

  axios.post(route('appointments.cancel', id))
  .then(response => {
    console.log('response:', response);

    if (response.data.success) {
      toast.success(response.data.message)
      // optionally reload or update the list
    }else{
      toast.error(response.data.message)
    }

  })
  .catch(error => {
    console.error(error);
    toast.error('Something went wrong!')
  });

}




</script>


<template>
  <div class="px-6 py-8 max-w-6xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-extrabold text-gray-800">Booking Details</h1>

      <Link
        href="/appointments/create"
        class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2 rounded-lg shadow transition"
      >
        + Book Appointment
      </Link>
    </div>

   <!-- Flash Success Message -->
  <!-- <div v-if="$page.props.flash?.success" class="bg-green-100 text-green-800 p-4 rounded mb-4">
    {{ $page.props.flash.success }}
  </div> -->

  <!-- Flash Error Message -->
  <!-- <div v-if="$page.props.flash?.error" class="bg-red-100 text-red-800 p-4 rounded mb-4">
    {{ $page.props.flash.error }}
  </div> -->


    <!-- GUESTS TABLE -->
    <div v-if="isGuestSection" class="overflow-x-auto bg-white shadow rounded-lg">
      <table class="min-w-full divide-y divide-gray-200 border">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sno</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created At</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr
            v-for="(guest, index) in appointments.data[0].guests"
            :key="guest.id"
          >
            <td class="px-6 py-4 text-sm text-gray-700">{{ index + 1 }}</td>
            <td class="px-6 py-4 text-sm font-semibold text-gray-800">{{ guest.email }}</td>
            <td class="px-6 py-4 text-sm text-gray-700">{{ guest.status == 1 ? 'Active' : 'In Active' }}</td>
            <td class="px-6 py-4 text-sm text-gray-600">{{ formatDateTime(guest.created_at) }}</td>
          </tr>
        </tbody>
      </table>

      <!-- Back Button -->
      <div class="mt-4">
        <Link
          :href="route('appointments.index')"
          class="text-blue-600 hover:underline"
        >
          ← Back to all appointments
        </Link>
      </div>
    </div>

    <!-- APPOINTMENTS TABLE -->
    <div v-else class="overflow-x-auto bg-white shadow rounded-lg">
      <table class="min-w-full divide-y divide-gray-200 border">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sno</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date & Time</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="(appointment, index) in appointments.data" :key="appointment.id">
            <td class="px-6 py-4 text-sm text-gray-700">{{ index + 1 }}</td>
            <td class="px-6 py-4 text-sm font-semibold text-gray-800">{{ appointment.title }}</td>
            <td class="px-6 py-4 text-sm text-gray-700">{{ appointment.description }}</td>
            <td class="px-6 py-4 text-sm text-gray-600">{{ formatDateTime(appointment.booking_date) }}</td>
            <td class="px-6 py-4 text-sm text-gray-600 space-x-2">
              <Link
                :href="route('appointments.index', { bookingId: appointment.id })"
                class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-2 py-1 rounded shadow"
                title="View Participants"
              >
                View
              </Link>
              <Link
                :href="`/appointments/${appointment.id}/edit`"
                class="bg-yellow-500 hover:bg-yellow-600 text-white font-medium px-2 py-1 rounded shadow"
                title="Edit Booking"
              >
                Edit
              </Link>
             
              <button
                @click.prevent="cancelAppointment(appointment.id)"
                class="bg-red-600 hover:bg-red-700 text-white font-medium px-2 py-1 rounded shadow"
                title="Cancel Appointment"
              >
                Cancel Appointment
              </button>

              <button
                
                @click.prevent="handleDelete(appointment)"
                class="bg-red-600 hover:bg-red-700 text-white font-medium px-2 py-1 rounded shadow"
                title="Delete Appointment"
              >
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- No Results -->
    <div v-if="appointments.data.length === 0" class="text-center text-gray-500 mt-8">
      No appointments found.
    </div>
  </div>
</template>
