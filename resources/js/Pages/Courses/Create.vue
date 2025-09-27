<script setup>
import { reactive, watch } from 'vue'
import { router } from '@inertiajs/vue3'

const form = reactive({
  title: '',
  slug: '',
  description: '',
  status: 'draft',
})

// Title değişince slug otomatik oluştur
watch(() => form.title, (t) => {
  form.slug = t
    .toLowerCase()
    .replace(/[^\w\s-]/g, '')
    .trim()
    .replace(/\s+/g, '-')
})

function submit() {
  router.post('/courses', form)
}
</script>

<template>
  <div class="p-6 max-w-xl space-y-3">
    <h1 class="text-xl font-semibold">New Course</h1>

    <input v-model="form.title" placeholder="Title" class="border p-2 w-full" />
    <input v-model="form.slug" placeholder="Slug" class="border p-2 w-full" />
    <textarea v-model="form.description" placeholder="Description" class="border p-2 w-full" />
    <select v-model="form.status" class="border p-2 w-full">
      <option value="draft">Draft</option>
      <option value="published">Published</option>
    </select>

    <button @click="submit" class="bg-black text-white px-4 py-2 rounded">Create</button>
  </div>
</template>
<script setup>
import { reactive, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'
import BackLink from '@/Components/BackLink.vue'
defineOptions({ layout: MainLayout })

const form = reactive({ title:'', slug:'', description:'', status:'draft' })
watch(() => form.title, (t) => {
  form.slug = t.toLowerCase().replace(/[^\w\s-]/g, '').trim().replace(/\s+/g, '-')
})
function submit(){ router.post('/courses', form) }
</script>

<template>
  <div class="p-6 max-w-xl space-y-3">
    <div class="flex items-center justify-between">
      <h1 class="text-xl font-semibold">New Course</h1>
      <BackLink />
    </div>

    <input v-model="form.title" placeholder="Title" class="border p-2 w-full" />
    <input v-model="form.slug" placeholder="Slug" class="border p-2 w-full" />
    <textarea v-model="form.description" placeholder="Description" class="border p-2 w-full" />
    <select v-model="form.status" class="border p-2 w-full">
      <option value="draft">Draft</option>
      <option value="published">Published</option>
    </select>
    <button @click="submit" class="bg-black text-white px-4 py-2 rounded">Create</button>
  </div>
</template>
