<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
defineOptions({ layout: AuthenticatedLayout })

const props = defineProps({
  courses: Object, // paginator
})

const authUser = usePage().props.auth?.user
</script>

<template>
  <Head title="Courses" />

  <div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-semibold">Courses</h1>

    <Link
      v-if="authUser && ['admin','instructor'].includes((authUser.role || '').toLowerCase())"
      href="/courses/create"
      class="px-3 py-1.5 rounded-xl border bg-white hover:bg-gray-50 text-sm"
    >
      + New Course
    </Link>
  </div>

  <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <div
      v-for="course in props.courses.data"
      :key="course.id"
      class="rounded-2xl border bg-white overflow-hidden"
    >
      <Link :href="`/courses/${course.id}`">
        <div class="h-40 bg-gray-100 flex items-center justify-center">
          <img
            v-if="course.cover_url"
            :src="course.cover_url"
            alt=""
            class="w-full h-40 object-cover"
          />
          <span v-else class="text-gray-400 text-sm">No cover</span>
        </div>
      </Link>

      <div class="p-4 space-y-1">
        <div class="text-xs text-gray-500">
          <span class="font-medium">Instructor:</span>
          <span>
            {{
              (course.instructor?.id && authUser?.id && course.instructor.id === authUser.id)
                ? 'You'
                : (course.instructor?.name ?? '—')
            }}
          </span>
        </div>

        <Link :href="`/courses/${course.id}`" class="block font-medium hover:underline">
          {{ course.title }}
        </Link>

        <div class="text-xs text-gray-500">
          {{ course.is_published ? 'Published' : 'Draft' }}
        </div>

        <div v-if="course.start_date" class="text-xs text-gray-600">
          Start: {{ new Date(course.start_date).toLocaleDateString('en-US') }}
        </div>

        <div class="text-right text-sm text-gray-700 mt-2">
          {{ course.price ? ('$' + Number(course.price).toLocaleString('en-US')) : 'Free' }}
        </div>
      </div>
    </div>
  </div>

  <!-- simple pagination -->
  <div class="mt-8 flex items-center gap-2">
    <Link
      v-if="props.courses.prev_page_url"
      :href="props.courses.prev_page_url"
      class="px-3 py-1.5 rounded-lg border bg-white text-sm"
    >« Previous</Link>
    <span class="text-sm">Page {{ props.courses.current_page }}</span>
    <Link
      v-if="props.courses.next_page_url"
      :href="props.courses.next_page_url"
      class="px-3 py-1.5 rounded-lg border bg-white text-sm"
    >Next »</Link>
  </div>
</template>
