<script setup>
import { Link, usePage } from '@inertiajs/vue3'
const user = usePage().props.auth?.user
</script>

<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Top Bar -->
    <nav class="bg-white border-b border-gray-200">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
          <!-- Left -->
          <div class="flex items-center space-x-6">
            <Link :href="route('dashboard')" class="font-semibold">Dashboard</Link>

            <!-- NAV EKLEMELERİ -->
            <Link :href="route('courses.index')" class="text-sm hover:underline">
              Courses
            </Link>

            <template v-if="user && (user.role === 'instructor' || user.role === 'admin')">
              <!-- MUTLAK LİNK -->
              <Link :href="route('courses.create')" class="text-sm hover:underline">
                + New Course
              </Link>
            </template>
            <!-- /NAV EKLEMELERİ -->
          </div>

          <!-- Right -->
          <div class="flex items-center space-x-4">
            <span class="text-sm" v-if="user">{{ user.name }}</span>
            <Link method="post" :href="route('logout')" as="button" class="text-sm hover:underline">
              Logout
            </Link>
          </div>
        </div>
      </div>
    </nav>

    <!-- Page -->
    <main>
      <slot />
    </main>
  </div>
</template>
