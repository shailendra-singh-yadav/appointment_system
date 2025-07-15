<script setup>
import Sidebar from './Admin/Sidebar.vue'
import Header from './Admin/Header.vue'
import Footer from './Admin/Footer.vue'
import { onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { useToast } from 'vue-toastification'


const toast = useToast()

// onMounted(() => {
//   const flash = usePage().props.flash

//   if (flash?.success) {
//     toast.success(flash.success)
//   }

//   if (flash?.error) {
//     toast.error(flash.error)
//   }
// })

</script>

<template>
  <div class="min-h-screen flex bg-gray-100">
    <!-- Sidebar -->
    <Sidebar />

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col">
      <!-- Header -->
      <Header>
        <slot name="header" />
      </Header>

      <!-- Main Content -->
      <main class="flex-1 overflow-y-auto">
        <slot />
      </main>

      <!-- Footer -->
      <Footer />
    </div>
  </div>
</template>
