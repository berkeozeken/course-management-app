<script setup>
import { Link, usePage } from '@inertiajs/vue3'

const user = usePage().props.auth?.user
defineProps({ courses: Array })
</script>

<template>
  <div class="p-6">
    <div class="flex items-center justify-between mb-4">
      <h1 class="text-2xl font-bold">Courses</h1>

      <!-- Sadece instructor/admin görsün -->
      <div v-if="user && (user.role === 'instructor' || user.role === 'admin')">
        <!-- MUTLAK LİNK -->
        <Link :href="route('courses.create')" class="underline">+ New Course</Link>
      </div>
    </div>

    <ul class="space-y-3">
      <li v-for="c in courses" :key="c.id" class="p-4 border rounded">
        <div class="font-semibold">{{ c.title }}</div>
        <div class="text-sm opacity-70">Enrollments: {{ c.enrollments_count }}</div>
        <Link :href="route('courses.show', c.slug)" class="underline">View</Link>
      </li>
    </ul>
  </div>
</template>
