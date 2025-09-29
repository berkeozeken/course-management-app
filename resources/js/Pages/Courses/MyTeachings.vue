<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
  courses: { type: Object, required: true }, // paginator
})

const fmt = (d) =>
  d ? new Date(d).toLocaleDateString('en-US', { month:'short', day:'numeric', year:'numeric' }) : null
</script>

<template>
  <Head title="Instructor" />
  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Instructor</h2>
      </div>
    </template>

    <div class="py-8">
      <!-- Empty state -->
      <div v-if="!courses.data.length" class="mx-auto max-w-3xl">
        <div class="rounded-2xl bg-white p-8 text-center shadow border">
          <h3 class="text-lg font-semibold text-gray-900">No courses yet</h3>
          <p class="mt-1 text-gray-600">When you create a course, it will appear here.</p>
        </div>
      </div>

      <div v-else class="mx-auto max-w-7xl grid gap-6 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
        <div v-for="c in courses.data" :key="c.id" class="bg-white rounded-2xl shadow-sm border overflow-hidden">
          <Link :href="route('courses.show', c.id)">
            <div class="h-40 bg-gray-100 flex items-center justify-center">
              <img v-if="c.cover_url" :src="c.cover_url" alt="" class="w-full h-40 object-cover" />
              <span v-else class="text-gray-400 text-sm">No cover</span>
            </div>
          </Link>

          <div class="p-4 space-y-2">
            <div class="flex items-center justify-between">
              <Link :href="route('courses.show', c.id)" class="font-semibold hover:underline truncate">
                {{ c.title }}
              </Link>
              <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                    :class="c.is_published ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'">
                {{ c.is_published ? 'Published' : 'Draft' }}
              </span>
            </div>

            <div class="text-xs text-gray-500">
              Start: <span class="font-medium text-gray-700">{{ fmt(c.start_date) || '—' }}</span>
            </div>

            <div class="flex items-center justify-between pt-2">
              <div class="text-sm text-gray-700">
                {{ c.price ? ('$' + Number(c.price).toLocaleString('en-US')) : 'Free' }}
              </div>
              <div class="flex items-center gap-2">
                <Link :href="route('courses.edit', c.id)"
                      class="rounded-lg border px-2.5 py-1 text-xs text-gray-700 bg-white hover:bg-gray-50">
                  Edit
                </Link>
                <Link :href="route('sections.create', c.id)"
                      class="rounded-lg border px-2.5 py-1 text-xs text-gray-700 bg-white hover:bg-gray-50">
                  + Section
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="courses.last_page > 1" class="mt-8 flex items-center gap-2">
        <Link v-if="courses.prev_page_url" :href="courses.prev_page_url"
              class="px-3 py-1.5 rounded-lg border bg-white text-sm">« Previous</Link>
        <span class="text-sm">Page {{ courses.current_page }} of {{ courses.last_page }}</span>
        <Link v-if="courses.next_page_url" :href="courses.next_page_url"
              class="px-3 py-1.5 rounded-lg border bg-white text-sm">Next »</Link>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
