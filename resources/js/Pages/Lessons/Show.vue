<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { onMounted, onBeforeUnmount } from 'vue'

const props = defineProps({
  lesson:   { type: Object, required: true },
  course:   { type: Object, required: true },
  nav:      { type: Object, required: true },   // { prev, next }
  is_last:  { type: Boolean, required: true },  // is this the last lesson?
  canWatch: { type: Boolean, required: true },
  canEdit:  { type: Boolean, required: true },
})

// Keyboard shortcuts: ← previous, → next, Enter = finish (if last)
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
            Back to Course
          </Link>
        </div>
      </div>
    </template>

    <div class="py-8">
      <div class="mx-auto max-w-4xl space-y-6">

        <!-- Top navigation -->
        <div class="rounded-xl bg-white p-4 shadow flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-500">Section</p>
            <p class="font-medium text-gray-800">{{ lesson.section?.title }}</p>
          </div>

          <div class="flex items-center gap-2">
            <Link v-if="nav.prev"
                  :href="route('lessons.show', nav.prev)"
                  class="rounded-lg border px-3 py-1.5 text-sm text-gray-700 bg-white hover:bg-gray-50"
                  title="Previous (←)">
              ← Previous
            </Link>
            <button v-else disabled class="rounded-lg border px-3 py-1.5 text-sm text-gray-400 bg-white cursor-not-allowed">
              ← Previous
            </button>

            <!-- Next / Finish -->
            <Link v-if="nav.next"
                  :href="route('lessons.show', nav.next)"
                  class="rounded-lg bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-700"
                  title="Next (→)">
              Next →
            </Link>
            <Link v-else
                  :href="route('courses.show', course.id)"
                  class="rounded-lg bg-emerald-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-emerald-700"
                  title="Finish Lesson (Enter)">
              ✓ Finish Lesson
            </Link>
          </div>
        </div>

        <!-- Content -->
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
              No content has been added for this lesson.
            </p>
          </template>

          <template v-else>
            <div class="rounded-lg bg-amber-50 border border-amber-200 p-4 text-amber-800">
              To watch this lesson, please <strong>enroll in the course</strong>, or ask the instructor to mark the
              lesson as <strong>free</strong>.
            </div>
          </template>
        </div>

        <!-- Bottom navigation -->
        <div class="flex items-center justify-between">
          <Link :href="route('courses.show', course.id)"
                class="rounded-lg border px-3 py-1.5 text-sm text-gray-700 bg-white hover:bg-gray-50">
            Back to Course
          </Link>

          <div class="flex items-center gap-2">
            <Link v-if="nav.prev"
                  :href="route('lessons.show', nav.prev)"
                  class="rounded-lg border px-3 py-1.5 text-sm text-gray-700 bg-white hover:bg-gray-50">
              ← Previous
            </Link>
            <span v-else class="text-sm text-gray-400">← Previous</span>

            <Link v-if="nav.next"
                  :href="route('lessons.show', nav.next)"
                  class="rounded-lg bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-700">
              Next →
            </Link>
            <Link v-else
                  :href="route('courses.show', course.id)"
                  class="rounded-lg bg-emerald-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-emerald-700">
              ✓ Finish Lesson
            </Link>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
