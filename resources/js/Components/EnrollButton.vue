<script setup>
import { router } from '@inertiajs/vue3'

const props = defineProps({
  courseId:     { type: Number,  required: true },
  isPublished:  { type: Boolean, required: true },
  enrolled:     { type: Boolean, default: false },
})

const enroll = () => {
  if (!props.isPublished || props.enrolled) return
  router.post(route('courses.enroll', props.courseId), {}, { preserveScroll: true })
}

const unenroll = () => {
  if (!props.enrolled) return
  if (!confirm('Do you want to unenroll from this course?')) return
  router.delete(route('courses.unenroll', props.courseId), { preserveScroll: true })
}
</script>

<template>
  <div class="flex gap-2">
    <!-- Not enrolled -->
    <button
      v-if="!enrolled"
      class="px-4 py-2 rounded-xl bg-indigo-600 text-white disabled:opacity-50 disabled:cursor-not-allowed w-full"
      :disabled="!isPublished"
      @click="enroll"
      :title="isPublished ? 'Enroll to this course' : 'This course is a draft, enrollment is not available.'"
    >
      Enroll
    </button>

    <!-- Enrolled -->
    <button
      v-else
      class="px-4 py-2 rounded-xl bg-gray-200 text-gray-800 w-full"
      @click="unenroll"
      title="You are enrolled — click to unenroll"
    >
      Unenroll
    </button>
  </div>
</template>
