<script setup>
import { Link, router, usePage } from '@inertiajs/vue3'

const page = usePage()
const user = page.props.auth?.user ?? null

function logout() {
  router.post('/logout')
}
</script>

<template>
  <div>
    <!-- Navbar -->
    <nav class="border-b bg-white">
      <div class="max-w-6xl mx-auto px-4 h-14 flex items-center justify-between">
        <div class="flex items-center gap-6">
          <Link href="/dashboard" class="font-semibold">Dashboard</Link>
          <Link href="/courses">Courses</Link>
        </div>

        <div class="flex items-center gap-4">
          <span v-if="user">{{ user.name }}</span>
          <button @click="logout" class="text-sm underline">Logout</button>
        </div>
      </div>
    </nav>

    <!-- Page container -->
    <main class="max-w-6xl mx-auto px-4 py-6">
      <slot />
    </main>
  </div>
</template>
