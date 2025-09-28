<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'
defineOptions({ layout: MainLayout })

const props = defineProps({ lesson: Object })

const form = useForm({
  title: props.lesson.title,
  content: props.lesson.content,
  video_url: props.lesson.video_url,
  duration_minutes: props.lesson.duration_minutes,
  is_free: !!props.lesson.is_free,
})

const submit = () => {
  form.put(`/lessons/${props.lesson.id}`, { preserveScroll: true })
}
</script>

<template>
  <Head :title="`Dersi Düzenle - ${lesson.title}`" />
  <div class="max-w-3xl mx-auto p-6">
    <div class="flex items-center justify-between mb-4">
      <h1 class="text-xl font-semibold">Dersi Düzenle</h1>
      <Link :href="`/courses/${lesson.section.course_id}`" class="text-sm text-gray-600 hover:underline">Geri</Link>
    </div>

    <div class="rounded-2xl border p-4 space-y-4">
      <div>
        <label class="block text-sm font-medium mb-1">Başlık</label>
        <input v-model="form.title" type="text" class="w-full rounded-lg border px-3 py-2" />
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">İçerik</label>
        <textarea v-model="form.content" rows="6" class="w-full rounded-lg border px-3 py-2" />
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Video URL</label>
        <input v-model="form.video_url" type="url" class="w-full rounded-lg border px-3 py-2" />
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium mb-1">Süre (dk)</label>
          <input v-model.number="form.duration_minutes" type="number" min="0" class="w-full rounded-lg border px-3 py-2" />
        </div>
        <label class="inline-flex items-center gap-2">
          <input v-model="form.is_free" type="checkbox" class="rounded border-gray-300" />
          <span>Ücretsiz önizleme</span>
        </label>
      </div>

      <div class="flex gap-2">
        <button @click="submit" :disabled="form.processing" class="rounded-lg border px-4 py-2 bg-black text-white disabled:opacity-50">Kaydet</button>
        <Link :href="`/courses/${lesson.section.course_id}`" class="rounded-lg border px-4 py-2">Vazgeç</Link>
      </div>
    </div>
  </div>
</template>
