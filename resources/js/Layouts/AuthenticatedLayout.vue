<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage()
const user = computed(() => page.props.auth?.user || null)
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <!-- NAV -->
    <header class="h-14 bg-white border-b">
      <div class="max-w-7xl mx-auto h-full px-4 flex items-center justify-between">
        <nav class="flex items-center gap-4">
          <Link href="/dashboard" class="font-semibold hover:underline">Dashboard</Link>
          <Link href="/courses" class="hover:underline">Courses</Link>
        </nav>

        <div class="flex items-center gap-3">
          <template v-if="user">
            <span class="text-sm text-gray-700">{{ user.name }}</span>
            <Link
              href="/logout"
              method="post"
              as="button"
              class="text-sm text-gray-600 hover:underline"
            >
              Logout
            </Link>
          </template>
          <template v-else>
            <Link href="/login" class="text-sm text-gray-600 hover:underline">Login</Link>
          </template>
        </div>
      </div>
    </header>

    <!-- OPTIONAL header slot -->
    <div v-if="$slots.header" class="bg-white border-b">
      <div class="max-w-7xl mx-auto px-4 py-4">
        <slot name="header" />
      </div>
    </div>

    <!-- CONTENT -->
    <main class="max-w-7xl mx-auto p-6">
      <slot />
    </main>
  </div>
</template>
