<script setup>
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-2xl font-semibold text-gray-800">Dashboard</h2>
    </template>

    <div class="bg-white rounded shadow p-6">
      <p>Welcome to your dashboard.</p>
    </div>
  </AuthenticatedLayout>
</template>
