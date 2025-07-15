<script setup>
import { ref, onMounted } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useToast } from 'vue-toastification'

const toast = useToast()

onMounted(() => {
  const flash = usePage().props.flash
  if (flash?.success) toast.success(flash.success)
  if (flash?.error) toast.error(flash.error)
})

const props = defineProps({ posts: Array })
const postsData = ref([...props.posts])
const dragStartIndex = ref(null)

// START: Drag Logic
function onDragStart(index) {
  dragStartIndex.value = index
}

function onDrop(dropIndex) {
  const startIndex = dragStartIndex.value
  if (startIndex === null || startIndex === dropIndex) return

  const movedItem = postsData.value[startIndex]
  postsData.value.splice(startIndex, 1)             // remove item
  postsData.value.splice(dropIndex, 0, movedItem)   // insert at new index
  dragStartIndex.value = null

  // Update backend
  const ordered = postsData.value.map((p, i) => ({
    id: p.id,
    order: i + 1,
  }))

  router.post('/posts/reorder', { ordered }, {
    preserveScroll: true,
    onSuccess: () => toast.success('Post reordered!'),
    onError: () => toast.error('Failed to reorder!'),
  })
}
// END: Drag Logic

function handleDelete(post, index) {
  if (!window.confirm('Are you sure you want to delete this post?')) return

  router.delete(`/posts/${post.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      postsData.value.splice(index, 1)
      toast.success('🗑️ Post moved to trash!')
    },
    onError: () => toast.error('❌ Failed to delete post'),
  })
}
</script>



<template>
  <AuthenticatedLayout>
    <!-- Header Slot -->
    <template #header>
      <h2 class="text-2xl font-semibold text-gray-800">📋 Manage Posts</h2>
    </template>

    <!-- Page Content -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Create Post Button -->
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-xl font-bold text-gray-700">All Posts</h1>
        <Link
          href="/posts/create"
          class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded shadow"
        >
          + Create Post
        </Link>
      </div>

      <!-- Posts Table -->
      <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-100 text-gray-700 text-sm uppercase">
            <tr>
              <th class="px-6 py-3 text-left">Title</th>
              <th class="px-6 py-3 text-left">Order</th>
              <th class="px-6 py-3 text-left">Content</th>
              <th class="px-6 py-3 text-left">Date</th>
              <th class="px-6 py-3 text-left">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr
              v-for="(post, index) in postsData"
              :key="post.id"
              draggable="true"
              @dragstart="onDragStart(index)"
              @dragover.prevent
              @drop="onDrop(index)"
              class="hover:bg-gray-50 transition cursor-move"
            >

              <td class="px-6 py-4 text-gray-800">{{ post.title }}</td>
              <td class="px-6 py-4 text-gray-800">{{ post.order }}</td>
              <td class="px-6 py-4 text-gray-800">{{ post.content }}</td>
              <td class="px-6 py-4 text-gray-800">{{ post.created_at }}</td>
              <td class="px-6 py-4 space-x-4">
                <Link
                  :href="`/posts/${post.id}/edit`"
                  class="text-blue-600 hover:underline"
                >
                 Edit
                </Link>

                <!-- delete button -->
                <button
                  @click.prevent="handleDelete(post, index)"
                  class="text-red-600 hover:underline"
                >
                  Delete
                </button>
              </td>

            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
