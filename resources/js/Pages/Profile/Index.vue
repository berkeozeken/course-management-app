<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, usePage, Link, useForm } from '@inertiajs/vue3'
import { computed, watch } from 'vue'

const page = usePage()
const user = computed(() => page.props.auth?.user || null)
const role = computed(() => (user.value?.role || '').toLowerCase())

const roleBadgeClasses = computed(() => ({
  student: 'bg-emerald-100 text-emerald-800',
  instructor: 'bg-amber-100 text-amber-800',
  admin: 'bg-sky-100 text-sky-800',
}[role.value] || 'bg-gray-100 text-gray-800'))

// --- Forms ---
const account = useForm({
  name:  user.value?.name  || '',
  email: user.value?.email || '',
})
// sayfa yenilenince props değişirse forma yansısın
watch(user, (u) => {
  account.name  = u?.name  || ''
  account.email = u?.email || ''
})

const passwordForm = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
})
</script>

<template>
  <Head title="Profile" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Profile</h2>
    </template>

    <div class="grid gap-6 lg:grid-cols-3">
      <!-- Account -->
      <section class="lg:col-span-2 space-y-6">
        <div class="rounded-2xl bg-white p-6 shadow-sm border space-y-4">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Account</h3>
            <span class="px-2 py-0.5 rounded-full text-xs font-medium" :class="roleBadgeClasses">
              {{ (user?.role || '').charAt(0).toUpperCase() + (user?.role || '').slice(1) }}
            </span>
          </div>

          <!-- success -->
          <p v-if="$page.props.flash?.success" class="text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg px-3 py-2">
            {{ $page.props.flash.success }}
          </p>

          <!-- account form -->
          <form @submit.prevent="account.put(route('profile.update'), { preserveScroll: true })" class="space-y-4">
            <div class="grid sm:grid-cols-2 gap-4">
              <label class="block">
                <span class="text-sm text-gray-700">Name</span>
                <input v-model="account.name" type="text" class="mt-1 w-full rounded-lg border-gray-300" />
                <span v-if="account.errors.name" class="text-xs text-red-600">{{ account.errors.name }}</span>
              </label>
              <label class="block">
                <span class="text-sm text-gray-700">Email</span>
                <input v-model="account.email" type="email" class="mt-1 w-full rounded-lg border-gray-300" />
                <span v-if="account.errors.email" class="text-xs text-red-600">{{ account.errors.email }}</span>
              </label>
            </div>

            <button :disabled="account.processing"
                    class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-white text-sm font-medium hover:bg-indigo-700 disabled:opacity-50">
              Save changes
            </button>
          </form>
        </div>

        <!-- password form -->
        <div class="rounded-2xl bg-white p-6 shadow-sm border space-y-4">
          <h3 class="text-lg font-semibold text-gray-900">Change Password</h3>

          <form @submit.prevent="passwordForm.put(route('profile.password'), { preserveScroll: true, onSuccess: () => passwordForm.reset() })"
                class="space-y-4">
            <div class="grid sm:grid-cols-3 gap-4">
              <label class="block sm:col-span-1">
                <span class="text-sm text-gray-700">Current password</span>
                <input v-model="passwordForm.current_password" type="password" class="mt-1 w-full rounded-lg border-gray-300" />
                <span v-if="passwordForm.errors.current_password" class="text-xs text-red-600">{{ passwordForm.errors.current_password }}</span>
              </label>
              <label class="block sm:col-span-1">
                <span class="text-sm text-gray-700">New password</span>
                <input v-model="passwordForm.password" type="password" class="mt-1 w-full rounded-lg border-gray-300" />
                <span v-if="passwordForm.errors.password" class="text-xs text-red-600">{{ passwordForm.errors.password }}</span>
              </label>
              <label class="block sm:col-span-1">
                <span class="text-sm text-gray-700">Confirm password</span>
                <input v-model="passwordForm.password_confirmation" type="password" class="mt-1 w-full rounded-lg border-gray-300" />
              </label>
            </div>

            <button :disabled="passwordForm.processing"
                    class="inline-flex items-center rounded-lg bg-gray-900 px-4 py-2 text-white text-sm font-medium hover:bg-black disabled:opacity-50">
              Update password
            </button>
          </form>
        </div>
      </section>

      <!-- Shortcuts -->
      <aside class="lg:col-span-1 space-y-4">
        <div class="rounded-2xl bg-white p-5 shadow-sm border">
          <h4 class="font-semibold text-gray-900">Shortcuts</h4>
          <div class="mt-3 space-y-2 text-sm">
            <Link href="/courses" class="block rounded-lg border px-3 py-2 bg-white hover:bg-gray-50">Browse Courses</Link>
            <Link v-if="role === 'student'" href="/my-courses" class="block rounded-lg border px-3 py-2 bg-white hover:bg-gray-50">My Courses</Link>
            <Link v-if="role === 'instructor' || role === 'admin'" href="/my-teachings" class="block rounded-lg border px-3 py-2 bg-white hover:bg-gray-50">My Teachings</Link>
          </div>
        </div>
      </aside>
    </div>
  </AuthenticatedLayout>
</template>
