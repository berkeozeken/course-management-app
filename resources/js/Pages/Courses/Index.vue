<script setup>
import { computed, reactive } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'
import BackLink from '@/Components/BackLink.vue'
defineOptions({ layout: MainLayout })

const page = usePage()
const user = page.props.auth?.user ?? null
const canManage = computed(() => ['admin','instructor'].includes(user?.role))
const courses = page.props.courses ?? []

const q = reactive({ text: '', status: 'all' })
const filtered = computed(() => {
  const t = q.text.toLowerCase()
  return courses.filter(c => {
    const textOk = !t || (c.title?.toLowerCase().includes(t) || c.slug?.toLowerCase().includes(t))
    const statusOk = q.status === 'all' || c.status === q.status
    return textOk && statusOk
  })
})

function destroyCourse(slug) {
  if (!confirm('Bu kursu silmek istiyor musun?')) return
  router.delete(`/courses/${slug}`, { preserveScroll: true })
}
</script>

<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold">Courses</h1>
      <BackLink />
    </div>

    <div class="flex items-center justify-between">
      <div class="flex gap-3 items-center w-full">
        <input v-model="q.text" placeholder="Search by title or slug..." class="border p-2 rounded w-full" />
        <select v-model="q.status" class="border p-2 rounded">
          <option value="all">All</option>
          <option value="draft">Draft</option>
          <option value="published">Published</option>
        </select>
      </div>

      <Link v-if="canManage" href="/courses/create" class="ml-4 bg-black text-white px-4 py-2 rounded">
        New Course
      </Link>
    </div>

    <div class="grid md:grid-cols-2 gap-4">
      <div v-for="c in filtered" :key="c.id" class="border rounded p-4 flex flex-col justify-between">
        <div class="space-y-1">
          <Link :href="`/courses/${c.slug}`" class="text-lg font-semibold hover:underline">{{ c.title }}</Link>
          <p class="text-sm text-gray-600">{{ c.description ?? '—' }}</p>
          <div class="text-xs text-gray-500">
            <span class="inline-block px-2 py-0.5 rounded border"
                  :class="c.status === 'published' ? 'border-green-500' : 'border-gray-400'">
              {{ c.status }}
            </span>
            <span class="ml-2">Enrollments: {{ c.enrollments_count ?? 0 }}</span>
          </div>
        </div>
        <div class="mt-4 flex gap-2">
          <Link :href="`/courses/${c.slug}`" class="px-3 py-2 border rounded">View</Link>
          <template v-if="canManage">
            <Link :href="`/courses/${c.slug}/edit`" class="px-3 py-2 border rounded">Edit</Link>
            <button @click="destroyCourse(c.slug)" class="px-3 py-2 border rounded">Delete</button>
          </template>
        </div>
      </div>
    </div>

    <div v-if="!filtered.length" class="text-sm text-gray-500">No courses found.</div>
  </div>
</template>
