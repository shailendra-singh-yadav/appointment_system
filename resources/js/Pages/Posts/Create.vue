<script setup>
import { useForm, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useToast } from 'vue-toastification'

const toast = useToast()

const props = defineProps({
  post: Object
})

const form = useForm({
  title: props.post?.title || '',
  content: props.post?.content || ''
})

const formTitle = props.post ? 'Edit Post' : 'Create Post'

function submit() {
  const isEdit = !!props.post

  if (isEdit) {
    form.put(`/posts/${props.post.id}`, {
      onSuccess: () => {
        toast.success('Post updated successfully!')
        router.visit('/posts')
      },
      onError: () => {
        toast.error('Failed to update post')
      }
    })
  } else {
    form.post('/posts', {
      onSuccess: () => {
        toast.success('Post created successfully!')
        router.visit('/posts')
      },
      onError: () => {
        toast.error('Failed to create post')
      }
    })
  }
}
</script>

<template>
  <AuthenticatedLayout>
    <!-- Header -->
    <template #header>
      <h2 class="text-2xl font-semibold text-gray-800">{{ formTitle }}</h2>
    </template>

    <!-- Form -->
    <div class="p-6 max-w-2xl mx-auto">
      <form @submit.prevent="submit" class="space-y-6 bg-white p-6 rounded shadow">
        <!-- Title -->
        <div>
          <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
          <input
            id="title"
            v-model="form.title"
            type="text"
            class="w-full border border-gray-300 p-2 rounded mt-1"
          />
          <div v-if="form.errors.title" class="text-red-600 text-sm mt-1">
            {{ form.errors.title }}
          </div>
        </div>

        <!-- Content -->
        <div>
          <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
          <textarea
            id="content"
            v-model="form.content"
            rows="6"
            class="w-full border border-gray-300 p-2 rounded mt-1"
          ></textarea>
          <div v-if="form.errors.content" class="text-red-600 text-sm mt-1">
            {{ form.errors.content }}
          </div>
        </div>

        <!-- Submit Button -->
        <div>
          <button
            type="submit"
            :disabled="form.processing"
            class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded"
          >
            💾 Save
          </button>
        </div>
      </form>
    </div>
  </AuthenticatedLayout>
</template>
