<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { computed, ref, onMounted, onBeforeUnmount } from 'vue'

const page = usePage()
const user = computed(() => page.props.auth?.user || null)
const role = computed(() => (user.value?.role || '').toLowerCase())

// aktif sekme
const current = computed(() => page.url || '/')
const isActive = (path) => current.value === path || current.value.startsWith(path + '/')

// mobil menü
const mobileOpen = ref(false)

// user dropdown (desktop)
const userMenuOpen = ref(false)
const toggleUserMenu = () => (userMenuOpen.value = !userMenuOpen.value)
const closeUserMenu = (e) => {
  if (!e.target.closest?.('#user-menu-root')) userMenuOpen.value = false
}
onMounted(() => document.addEventListener('click', closeUserMenu))
onBeforeUnmount(() => document.removeEventListener('click', closeUserMenu))
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <!-- NAV -->
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b">
      <div class="mx-auto max-w-7xl px-4">
        <div class="h-14 flex items-center justify-between">
          <!-- Sol -->
          <div class="flex items-center gap-6">
            <!-- Brand -> Dashboard -->
            <Link href="/dashboard" class="flex items-center gap-2">
              <div class="h-8 w-8 rounded-xl bg-indigo-600 text-white grid place-content-center font-bold">C</div>
              <span class="font-semibold tracking-tight">CourseApp</span>
            </Link>

            <!-- Tabs (desktop) -->
            <nav class="hidden md:flex items-center gap-1">
              <Link
                href="/courses"
                :class="[
                  'px-3 py-2 rounded-lg text-sm font-medium',
                  isActive('/courses') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
                ]"
              >Courses</Link>

              <Link
                v-if="role === 'student'"
                href="/my-courses"
                :class="[
                  'px-3 py-2 rounded-lg text-sm font-medium',
                  isActive('/my-courses') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
                ]"
              >My Courses</Link>

              <Link
                v-if="role === 'instructor' || role === 'admin'"
                href="/my-teachings"
                :class="[
                  'px-3 py-2 rounded-lg text-sm font-medium',
                  isActive('/my-teachings') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
                ]"
              >My Teachings</Link>
            </nav>
          </div>

          <!-- Sağ -->
          <div class="flex items-center gap-3">
            <!-- Rol rozeti (dropdown DIŞINDA) -->
            <span v-if="user" class="hidden md:inline-flex px-2 py-0.5 rounded-full text-xs font-medium"
                  :class="{
                    'bg-emerald-100 text-emerald-800': role === 'student',
                    'bg-amber-100 text-amber-800': role === 'instructor',
                    'bg-sky-100 text-sky-800': role === 'admin'
                  }">
              {{ (user.role || '').charAt(0).toUpperCase() + (user.role || '').slice(1) }}
            </span>

            <!-- Desktop user dropdown (sadece isim + chevron) -->
            <div v-if="user" id="user-menu-root" class="relative hidden md:block">
              <button
                @click="toggleUserMenu"
                class="inline-flex items-center gap-2 rounded-lg border px-3 py-1.5 text-sm bg-white hover:bg-gray-50"
              >
                <span class="font-medium text-gray-800">{{ user.name }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.08 1.04l-4.25 4.25a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
                </svg>
              </button>

              <div
                v-show="userMenuOpen"
                class="absolute right-0 mt-2 w-44 overflow-hidden rounded-xl border bg-white shadow-lg"
              >
                <Link
                  href="/profile"
                  class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50"
                  @click="userMenuOpen = false"
                >Profile</Link>

                <Link
                  href="/logout"
                  method="post"
                  as="button"
                  class="block w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-red-50"
                  @click="userMenuOpen = false"
                >Logout</Link>
              </div>
            </div>

            <!-- Mobile toggle -->
            <button
              class="md:hidden inline-flex items-center rounded-lg border px-2.5 py-1.5 text-sm"
              @click="mobileOpen = !mobileOpen"
            >
              Menu
            </button>
          </div>
        </div>
      </div>

      <!-- Mobile nav -->
      <div v-if="mobileOpen" class="md:hidden border-t bg-white">
        <div class="mx-auto max-w-7xl px-4 py-2 space-y-1">
          <Link href="/courses" class="block px-3 py-2 rounded-lg text-sm"
                :class="isActive('/courses') ? 'bg-indigo-50 text-indigo-700' : 'hover:bg-gray-50'">Courses</Link>
          <Link v-if="role === 'student'" href="/my-courses" class="block px-3 py-2 rounded-lg text-sm"
                :class="isActive('/my-courses') ? 'bg-indigo-50 text-indigo-700' : 'hover:bg-gray-50'">My Courses</Link>
          <Link v-if="role === 'instructor' || role === 'admin'" href="/my-teachings" class="block px-3 py-2 rounded-lg text-sm"
                :class="isActive('/my-teachings') ? 'bg-indigo-50 text-indigo-700' : 'hover:bg-gray-50'">My Teachings</Link>

          <div class="border-t my-2"></div>
          <!-- mobilde de profile/logout -->
          <div class="px-3 pb-1 text-xs text-gray-500">Signed in as {{ user?.name }}</div>
          <Link href="/profile" class="block px-3 py-2 rounded-lg text-sm hover:bg-gray-50">Profile</Link>
          <Link href="/logout" method="post" as="button" class="block w-full text-left px-3 py-2 rounded-lg text-sm text-red-600 hover:bg-red-50">
            Logout
          </Link>
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
