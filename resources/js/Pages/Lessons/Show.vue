<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { onMounted, onBeforeUnmount } from 'vue'

const props = defineProps({
  lesson:   { type: Object, required: true },
  course:   { type: Object, required: true },
  nav:      { type: Object, required: true },   // { prev, next }
  is_last:  { type: Boolean, required: true },  // <<< son ders mi?
  canWatch: { type: Boolean, required: true },
  canEdit:  { type: Boolean, required: true },
})

// Klavye kısayolları: ← önceki, → sonraki, Enter = dersi bitir (son dersteyse)
const onKey = (e) => {
  const tag = (e.target?.tagName || '').toUpperCase()
  if (tag === 'INPUT' || tag === 'TEXTAREA') return
  if (e.key === 'ArrowRight' && props.nav.next) window.location.href = route('lessons.show', props.nav.next)
  if (e.key === 'ArrowLeft'  && props.nav.prev) window.location.href = route('lessons.show', props.nav.prev)
  if (e.key === 'Enter' && props.is_last) window.location.href = route('courses.show', props.course.id)
}

onMounted(() => window.addEventListener('keydown', onKey))
onBeforeUnmount(() => window.removeEventListener('keydown', onKey))
</script>

<template>
  <Head :title="lesson.title" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between gap-3">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
          {{ lesson.title }}
        </h2>

        <div class="flex items-center gap-2">
          <Link :href="route('courses.show', course.id)"
                class="rounded-lg border px-3 py-1.5 text-sm text-gray-700 bg-white hover:bg-gray-50">
            Kursa Dön
          </Link>
        </div>
      </div>
    </template>

    <div class="py-8">
      <div class="mx-auto max-w-4xl space-y-6">

        <!-- Üst navigasyon -->
        <div class="rounded-xl bg-white p-4 shadow flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-500">Bölüm</p>
            <p class="font-medium text-gray-800">{{ lesson.section?.title }}</p>
          </div>

          <div class="flex items-center gap-2">
            <Link v-if="nav.prev"
                  :href="route('lessons.show', nav.prev)"
                  class="rounded-lg border px-3 py-1.5 text-sm text-gray-700 bg-white hover:bg-gray-50"
                  title="Önceki (←)">
              ← Önceki
            </Link>
            <button v-else disabled class="rounded-lg border px-3 py-1.5 text-sm text-gray-400 bg-white cursor-not-allowed">
              ← Önceki
            </button>

            <!-- Sonraki / Dersi Bitir -->
            <Link v-if="nav.next"
                  :href="route('lessons.show', nav.next)"
                  class="rounded-lg bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-700"
                  title="Sonraki (→)">
              Sonraki →
            </Link>
            <Link v-else
                  :href="route('courses.show', course.id)"
                  class="rounded-lg bg-emerald-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-emerald-700"
                  title="Dersi Bitir (Enter)">
              ✓ Dersi Bitir
            </Link>
          </div>
        </div>

        <!-- İçerik -->
        <div class="rounded-2xl bg-white p-6 shadow space-y-4">
          <template v-if="canWatch">
            <div v-if="lesson.video_url" class="w-full">
              <iframe
                :src="lesson.video_url"
                class="w-full rounded-xl"
                style="aspect-ratio: 16/9;"
                allowfullscreen
              />
            </div>
            <div v-if="lesson.content" class="prose max-w-none">
              <div v-html="lesson.content"></div>
            </div>
            <p v-if="!lesson.video_url && !lesson.content" class="text-gray-500">
              Bu ders için içerik eklenmemiş.
            </p>
          </template>

          <template v-else>
            <div class="rounded-lg bg-amber-50 border border-amber-200 p-4 text-amber-800">
              Bu dersi izlemek için önce <strong>kursa kayıt ol</strong> ya da ders ücretsiz ise
              eğitmen tarafından <strong>ücretsiz</strong> işaretlenmesini bekle.
            </div>
          </template>
        </div>

        <!-- Alt navigasyon -->
        <div class="flex items-center justify-between">
          <Link :href="route('courses.show', course.id)"
                class="rounded-lg border px-3 py-1.5 text-sm text-gray-700 bg-white hover:bg-gray-50">
            Kursa Dön
          </Link>

          <div class="flex items-center gap-2">
            <Link v-if="nav.prev"
                  :href="route('lessons.show', nav.prev)"
                  class="rounded-lg border px-3 py-1.5 text-sm text-gray-700 bg-white hover:bg-gray-50">
              ← Önceki
            </Link>
            <span v-else class="text-sm text-gray-400">← Önceki</span>

            <Link v-if="nav.next"
                  :href="route('lessons.show', nav.next)"
                  class="rounded-lg bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-700">
              Sonraki →
            </Link>
            <Link v-else
                  :href="route('courses.show', course.id)"
                  class="rounded-lg bg-emerald-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-emerald-700">
              ✓ Dersi Bitir
            </Link>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
