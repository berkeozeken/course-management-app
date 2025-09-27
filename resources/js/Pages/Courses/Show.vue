<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'
import BackLink from '@/Components/BackLink.vue'
import LessonForm from '@/Components/LessonForm.vue'
import EnrollButton from '@/Components/EnrollButton.vue'
defineOptions({ layout: MainLayout })

const page = usePage()
const course = page.props.course
const canManage = page.props.canManage
const enrollmentStatus = page.props.enrollmentStatus

const sections = computed(() => Array.isArray(course.sections) ? course.sections : [])
const flatLessons = computed(() => sections.value.flatMap(s => (Array.isArray(s.lessons) ? s.lessons : [])))
</script>

<template>
  <div class="space-y-8">
    <div class="flex items-start justify-between">
      <div>
        <BackLink />
        <h1 class="text-3xl font-semibold mt-2">{{ course.title }}</h1>
        <p class="text-sm text-gray-600">
          Instructor: <span class="font-medium">{{ course.owner?.name ?? '—' }}</span>
        </p>
        <p v-if="course.status" class="text-xs inline-block px-2 py-0.5 rounded border"
           :class="course.status === 'published' ? 'border-green-500' : 'border-gray-400'">
          {{ course.status }}
        </p>
      </div>

      <div class="shrink-0" v-if="!canManage">
        <EnrollButton :course-id="course.id" :initial-status="enrollmentStatus" />
      </div>
    </div>

    <section v-if="course.description" class="prose max-w-none">
      <p class="whitespace-pre-line">{{ course.description }}</p>
    </section>

    <section class="space-y-4">
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold">Lessons</h2>
        <Link v-if="canManage" :href="`/courses/${course.slug}/edit`" class="text-sm underline">Edit course</Link>
      </div>

      <template v-if="sections.length">
        <div v-for="sec in sections" :key="sec.id" class="border rounded">
          <div class="px-4 py-2 border-b bg-gray-50 font-medium">
            {{ sec.title ?? 'Section' }}
            <span v-if="sec.lessons?.length" class="text-xs text-gray-500">({{ sec.lessons.length }})</span>
          </div>
          <ul class="p-4 space-y-2">
            <li v-for="l in (sec.lessons ?? [])" :key="l.id" class="flex items-center justify-between">
              <div>
                <span class="font-medium">{{ l.title }}</span>
                <span v-if="l.is_free" class="ml-2 text-xs px-2 py-0.5 border rounded">free</span>
              </div>
            </li>
            <li v-if="!(sec.lessons?.length)" class="text-sm text-gray-500">No lessons in this section.</li>
          </ul>
        </div>
      </template>

      <template v-else>
        <ul v-if="flatLessons.length" class="space-y-2">
          <li v-for="l in flatLessons" :key="l.id" class="flex items-center justify-between border rounded px-4 py-2">
            <span class="font-medium">{{ l.title }}</span>
            <span v-if="l.is_free" class="text-xs px-2 py-0.5 border rounded">free</span>
          </li>
        </ul>
        <div v-else class="text-sm text-gray-500">No lessons yet.</div>
      </template>
    </section>

    <section v-if="canManage" class="border rounded p-4">
      <h3 class="font-semibold mb-2">Add Lesson</h3>
      <LessonForm :course-id="course.id" />
    </section>
  </div>
</template>
