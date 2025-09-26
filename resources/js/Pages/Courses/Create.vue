<script setup>
import { useForm, Link } from '@inertiajs/vue3'

const form = useForm({
  title: '',
  slug: '',
  description: '',
  status: 'draft',
})

function submit() {
  form.post(route('courses.store'))
}
</script>

<template>
  <div class="p-6 max-w-2xl mx-auto space-y-4">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold">New Course</h1>
      <Link :href="route('courses.index')" class="underline">← Back</Link>
    </div>

    <div class="space-y-3">
      <div>
        <label class="block text-sm font-medium mb-1">Title</label>
        <input v-model="form.title" type="text" class="border rounded w-full p-2" placeholder="Laravel + Vue 201" />
        <div v-if="form.errors.title" class="text-red-600 text-sm mt-1">{{ form.errors.title }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Slug</label>
        <input v-model="form.slug" type="text" class="border rounded w-full p-2" placeholder="laravel-vue-201" />
        <div v-if="form.errors.slug" class="text-red-600 text-sm mt-1">{{ form.errors.slug }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Description</label>
        <textarea v-model="form.description" rows="4" class="border rounded w-full p-2" placeholder="Short course summary..."></textarea>
        <div v-if="form.errors.description" class="text-red-600 text-sm mt-1">{{ form.errors.description }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Status</label>
        <select v-model="form.status" class="border rounded w-full p-2">
          <option value="draft">draft</option>
          <option value="published">published</option>
        </select>
        <div v-if="form.errors.status" class="text-red-600 text-sm mt-1">{{ form.errors.status }}</div>
      </div>

      <div class="pt-2">
        <button @click="submit" :disabled="form.processing" class="border rounded px-4 py-2">
          <span v-if="form.processing">Saving...</span>
          <span v-else>Create</span>
        </button>
      </div>

      <div v-if="form.recentlySuccessful" class="text-green-700 text-sm">
        Saved!
      </div>
    </div>
  </div>
</template>
