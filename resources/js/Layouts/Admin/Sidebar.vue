<script setup>
import { Link } from '@inertiajs/vue3'

// Heroicons
import { HomeIcon } from '@heroicons/vue/24/solid'
import { CalendarDaysIcon } from '@heroicons/vue/24/solid'
import { PencilSquareIcon } from '@heroicons/vue/24/solid'
import { ChatBubbleLeftIcon } from '@heroicons/vue/24/solid'
</script>

<template>
  <aside class="w-64 bg-white border-r hidden sm:block">
    <div class="p-6 border-b flex items-center space-x-2">
      <span class="text-lg font-bold text-gray-700">My App</span>
    </div>
    <nav class="p-4 space-y-2">
      
      <Link
        :href="route('dashboard')"
        class="block px-4 py-2 rounded text-gray-700 hover:bg-indigo-100 flex items-center gap-2"
        :class="{ 'bg-indigo-100 font-semibold': route().current('dashboard') }"
      >
        <HomeIcon class="w-5 h-5" />
        Dashboard
      </Link>

      <Link
        :href="route('appointments.index')"
        class="block px-4 py-2 rounded text-gray-700 hover:bg-indigo-100 flex items-center gap-2"
        :class="{ 'bg-indigo-100 font-semibold': route().current('appointments.index') }"
      >
        <CalendarDaysIcon class="w-5 h-5" />
        Appointments
      </Link>

      <Link
        :href="route('posts.index')"
        class="block px-4 py-2 rounded text-gray-700 hover:bg-indigo-100 flex items-center gap-2"
        :class="{ 'bg-indigo-100 font-semibold': route().current('posts.index') }"
      >
        <PencilSquareIcon class="w-5 h-5" />
        Posts
      </Link>

      <Link
        :href="route('chat.index')"
        class="block px-4 py-2 rounded text-gray-700 hover:bg-indigo-100 flex items-center gap-2"
        :class="{ 'bg-indigo-100 font-semibold': route().current('chat.index') }"
      >
        <ChatBubbleLeftIcon class="w-5 h-5" />
        Chat
      </Link>

    </nav>
  </aside>
</template>

