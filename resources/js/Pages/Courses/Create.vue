<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

const form = useForm({
  title: '',
  description: '',
  price: '',
  cover_url: '',
  is_published: false,
  start_date: '', // YYYY-MM-DD
})

const submit = () => {
  form.post(route('courses.store'), { preserveScroll: true })
}
</script>

<template>
  <Head title="Create Course" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Create Course</h2>
        <Link
          :href="route('courses.index')"
          class="rounded-lg border px-3 py-1.5 text-sm text-gray-700 bg-white hover:bg-gray-50"
        >
          ← Courses
        </Link>
      </div>
    </template>

    <div class="py-8">
      <div class="mx-auto max-w-3xl">
        <form @submit.prevent="submit" class="space-y-6 bg-white rounded-2xl p-6 shadow">
          <!-- Title -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Title *</label>
            <input
              v-model="form.title"
              type="text"
              class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
              placeholder="e.g., Inertia.js with Vue 3"
              required
            />
            <div v-if="form.errors.title" class="mt-1 text-sm text-red-600">{{ form.errors.title }}</div>
          </div>

          <!-- Description -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Description</label>
            <textarea
              v-model="form.description"
              rows="5"
              class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
              placeholder="Course description..."
            />
            <div v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</div>
          </div>

          <!-- Price & Published -->
          <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
              <label class="block text-sm font-medium text-gray-700">Price</label>
              <input
                v-model="form.price"
                type="number"
                min="0"
                step="0.01"
                class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="Leave empty for free"
              />
              <div v-if="form.errors.price" class="mt-1 text-sm text-red-600">{{ form.errors.price }}</div>
            </div>

            <div class="flex items-center gap-3 pt-6">
              <input
                id="is_published"
                v-model="form.is_published"
                type="checkbox"
                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
              />
              <label for="is_published" class="text-sm text-gray-700">Published</label>
            </div>
          </div>

          <!-- Cover URL -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Cover URL</label>
            <input
              v-model="form.cover_url"
              type="url"
              class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
              placeholder="https://..."
            />
            <div v-if="form.errors.cover_url" class="mt-1 text-sm text-red-600">{{ form.errors.cover_url }}</div>
          </div>

          <!-- Start Date -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Start Date</label>
            <input
              v-model="form.start_date"
              type="date"
              class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
            />
            <div v-if="form.errors.start_date" class="mt-1 text-sm text-red-600">{{ form.errors.start_date }}</div>
          </div>

          <!-- Actions -->
          <div class="flex items-center justify-end gap-3">
            <Link
              :href="route('courses.index')"
              class="rounded-lg border px-4 py-2 text-sm text-gray-700 bg-white hover:bg-gray-50"
            >
              Cancel
            </Link>
            <button
              type="submit"
              :disabled="form.processing"
              class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50"
            >
              Save
            </button>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
