<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
  course: { type: Object, required: true },
})

const form = useForm({
  title: props.course.title ?? '',
  description: props.course.description ?? '',
  price: props.course.price ?? '',
  cover_url: props.course.cover_url ?? '',
  is_published: Boolean(props.course.is_published),
})

const submit = () => {
  form.put(route('courses.update', props.course.id), {
    preserveScroll: true,
  })
}
</script>

<template>
  <Head :title="`Kursu Düzenle - ${course.title}`" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
          Kursu Düzenle
        </h2>
        <Link
          :href="route('courses.show', course.id)"
          class="rounded-lg border px-3 py-1.5 text-sm text-gray-700 bg-white hover:bg-gray-50"
        >
          ← Kursa Dön
        </Link>
      </div>
    </template>

    <div class="py-8">
      <div class="mx-auto max-w-3xl">
        <form @submit.prevent="submit" class="space-y-6 bg-white rounded-2xl p-6 shadow">
          <div>
            <label class="block text-sm font-medium text-gray-700">Başlık *</label>
            <input
              v-model="form.title"
              type="text"
              class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
              required
            />
            <div v-if="form.errors.title" class="mt-1 text-sm text-red-600">{{ form.errors.title }}</div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Açıklama</label>
            <textarea
              v-model="form.description"
              rows="5"
              class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
            />
            <div v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</div>
          </div>

          <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
              <label class="block text-sm font-medium text-gray-700">Fiyat (₺)</label>
              <input
                v-model="form.price"
                type="number"
                min="0"
                step="1"
                class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
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
              <label for="is_published" class="text-sm text-gray-700">Yayınla</label>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Kapak Görseli URL</label>
            <input
              v-model="form.cover_url"
              type="url"
              class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
            />
            <div v-if="form.errors.cover_url" class="mt-1 text-sm text-red-600">{{ form.errors.cover_url }}</div>
          </div>

          <div class="flex items-center justify-end gap-3">
            <Link
              :href="route('courses.show', course.id)"
              class="rounded-lg border px-4 py-2 text-sm text-gray-700 bg-white hover:bg-gray-50"
            >
              İptal
            </Link>
            <button
              type="submit"
              :disabled="form.processing"
              class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50"
            >
              Güncelle
            </button>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
