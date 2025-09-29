<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage()
const user = computed(() => page.props.auth?.user || null)
const role = computed(() => (user.value?.role || '').toLowerCase())

const shortcuts = computed(() => {
  if (role.value === 'student') {
    return [
      { title: 'Browse Courses', href: '/courses', desc: 'Find a course to enroll' },
      { title: 'My Courses', href: '/my-courses', desc: 'Continue learning' },
    ]
  }
  return [
    { title: 'All Courses', href: '/courses', desc: 'Create, edit and manage' },
    { title: 'Create Course', href: '/courses/create', desc: 'Add a new course' },
    { title: 'Teaching', href: '/my-teachings', desc: 'Your authored courses' },
  ]
})

// pro tipler
const proTip = computed(() => {
  switch (role.value) {
    case 'student':
      return {
        title: 'Pro tip',
        lines: [
          'Use ← / → to navigate lessons',
          'Press Enter on the last lesson to finish',
        ]
      }
    case 'instructor':
      return {
        title: 'Pro tip for instructors',
        lines: [
          'Add Sections first, then Lessons for better flow',
          'Toggle Publish/Draft from the course page',
          'Check Participants to see enrolled students',
        ]
      }
    case 'admin':
      return {
        title: 'Pro tip for admins',
        lines: [
          'Admins can edit or delete any course',
          'Draft courses stay hidden from students',
          'Use Teaching tab to see authored courses',
        ]
      }
    default:
      return { title: 'Pro tip', lines: [] }
  }
})
</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Dashboard</h2>
    </template>

    <div class="grid gap-6 lg:grid-cols-3">
      <!-- welcome & shortcuts -->
      <section class="lg:col-span-2">
        <div class="rounded-2xl bg-white p-6 shadow-sm border">
          <h3 class="text-lg font-semibold text-gray-900">
            Welcome, {{ user?.name || 'User' }} 👋
          </h3>
          <p class="mt-1 text-gray-600">Quick access to what you need today.</p>

          <div class="mt-6 grid sm:grid-cols-2 gap-4">
            <Link
              v-for="s in shortcuts"
              :key="s.href"
              :href="s.href"
              class="block rounded-xl border p-4 hover:shadow-sm hover:border-indigo-300 transition"
            >
              <div class="font-semibold text-gray-900">{{ s.title }}</div>
              <div class="text-sm text-gray-600 mt-0.5">{{ s.desc }}</div>
            </Link>
          </div>
        </div>
      </section>

      <!-- pro tip (status kaldırıldı) -->
      <aside class="lg:col-span-1 space-y-4">
        <div class="rounded-2xl bg-indigo-50 border border-indigo-100 p-5 text-indigo-900">
          <div class="font-semibold">{{ proTip.title }}</div>
          <ul class="mt-2 list-disc pl-5 text-sm space-y-1">
            <li v-for="(line, i) in proTip.lines" :key="i">{{ line }}</li>
          </ul>
        </div>
      </aside>
    </div>
  </AuthenticatedLayout>
</template>
