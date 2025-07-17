<script setup>
import MainLayout from '@/Layouts/AuthenticatedLayout.vue'
import { defineProps } from 'vue'
import { Link } from '@inertiajs/vue3'

defineOptions({ layout: MainLayout })

const props = defineProps({
  appointments: Object, // from controller: Appointment::latest()->paginate()
  filters: Object,
})

console.log('props:',props.appointments)
const formatDateTime = (datetime) => {
  if (!datetime) return '-'
  const parsedDate = new Date(datetime) // renamed to avoid conflict
  return isNaN(parsedDate)
    ? 'Invalid Date'
    : parsedDate.toLocaleString('en-IN', {
        dateStyle: 'medium',
        timeStyle: 'short',
      })
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
    <div v-if="$page.props.flash?.success" class="bg-green-100 text-green-800 p-4 rounded mb-4">
      {{ $page.props.flash.success }}
    </div>

    <!-- Appointment Table -->
    <div class="overflow-x-auto bg-white shadow rounded-lg">
      <table class="min-w-full divide-y divide-gray-200 border">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sno</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date & Time</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="(appointment, index) in appointments.data" :key="appointment.id">
            <td class="px-6 py-4 text-sm text-gray-700">{{ index + 1 }}</td>
            <td class="px-6 py-4 text-sm font-semibold text-gray-800">{{ appointment.title }}</td>
            <td class="px-6 py-4 text-sm text-gray-700">{{ appointment.description }}</td>
            <td class="px-6 py-4 text-sm text-gray-600">{{ formatDateTime(appointment.date) }}</td>

           <Link
  :href="`/appointments/index?bookingId=${appointment.id}`"
  class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-2 py-2 rounded-lg shadow transition"
>
  View participants
</Link>



           <Link
            :href="`/appointments/${appointment.id}/edit`"
            class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-2 py-2 rounded-lg shadow transition"
            >
            Edit
            </Link>

            <Link
            :href="`/appointments/${appointment.id}/cancel`"
            class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-2 py-2 rounded-lg shadow transition"
            >
             Appointment Cancel
            </Link>

          </tr>
        </tbody>
      </table>
    </div>

    <!-- No Appointments -->
    <div v-if="appointments.length === 0" class="text-center text-gray-500 mt-8">
      No appointments found.
    </div>
  </div>
</template>
