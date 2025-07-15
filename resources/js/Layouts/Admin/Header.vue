<script setup>
import { Link, usePage } from '@inertiajs/vue3'

const user = usePage().props.auth.user
</script>

<template>
  <header class="bg-white shadow px-6 py-4 flex justify-between items-center border-b">
    <div>
      <slot />
    </div>
    <div class="flex items-center space-x-4">
      <span class="text-gray-600 text-sm">Hi, {{ user.name }}</span>
      <Link :href="route('profile.edit')" class="text-sm text-gray-600 hover:underline">Profile</Link>
      <Link
        :href="route('logout')"
        method="post"
        as="button"
        class="text-sm text-red-500 hover:underline"
      >
        Logout
      </Link>
    </div>
  </header>
</template>
