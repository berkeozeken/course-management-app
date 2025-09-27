<script setup>
import { reactive } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'
import BackLink from '@/Components/BackLink.vue'
defineOptions({ layout: MainLayout })

const page = usePage()
const course = page.props.course
const form = reactive({
  title: course.title, slug: course.slug,
  description: course.description ?? '', status: course.status ?? 'draft'
})
function submit(){ router.patch(`/courses/${course.slug}`, form) }
</script>

<template>
  <div class="p-6 max-w-xl space-y-3">
    <div class="flex items-center justify-between">
      <h1 class="text-xl font-semibold">Edit Course</h1>
      <BackLink />
    </div>

    <input v-model="form.title" class="border p-2 w-full" />
    <input v-model="form.slug" class="border p-2 w-full" />
    <textarea v-model="form.description" class="border p-2 w-full" />
    <select v-model="form.status" class="border p-2 w-full">
      <option value="draft">Draft</option>
      <option value="published">Published</option>
    </select>
    <button @click="submit" class="bg-black text-white px-4 py-2 rounded">Update</button>
  </div>
</template>
