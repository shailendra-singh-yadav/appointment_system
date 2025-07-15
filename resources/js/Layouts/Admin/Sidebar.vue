<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<template>
  <aside class="w-64 bg-white border-r hidden sm:block">
    <div class="p-6 border-b flex items-center space-x-2">
      <span class="text-lg font-bold text-gray-700">My App</span>
    </div>
    <nav class="p-4 space-y-2">
      <Link
        :href="route('dashboard')"
        class="block px-4 py-2 rounded text-gray-700 hover:bg-indigo-100"
        :class="{ 'bg-indigo-100 font-semibold': route().current('dashboard') }"
      >
        🏠 Dashboard
      </Link>
      <Link
        :href="route('appointments.index')"
        class="block px-4 py-2 rounded text-gray-700 hover:bg-indigo-100"
        :class="{ 'bg-indigo-100 font-semibold': route().current('appointments.index') }"
      >
        📅 Appointments
      </Link>
      <Link
        :href="route('posts.index')"
        class="block px-4 py-2 rounded text-gray-700 hover:bg-indigo-100"
        :class="{ 'bg-indigo-100 font-semibold': route().current('posts.index') }"
      >
        📝 Posts
      </Link>
    </nav>
  </aside>
</template>
