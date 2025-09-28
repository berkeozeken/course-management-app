<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  role: 'student', // student | instructor
});

const submit = () => {
  form.post(route('register'), {
    onFinish: () => form.reset('password', 'password_confirmation'),
  });
};
</script>

<template>
  <GuestLayout>
    <Head title="Register" />

    <h1 class="text-2xl font-bold text-gray-900 mb-6">Create your account</h1>

    <form @submit.prevent="submit" class="space-y-6">
      <div>
        <InputLabel for="name" value="Name" />
        <TextInput
          id="name"
          v-model="form.name"
          type="text"
          class="mt-1 block w-full"
          autocomplete="name"
          required
        />
        <InputError class="mt-2" :message="form.errors.name" />
      </div>

      <div>
        <InputLabel for="email" value="Email" />
        <TextInput
          id="email"
          v-model="form.email"
          type="email"
          class="mt-1 block w-full"
          autocomplete="username"
          required
        />
        <InputError class="mt-2" :message="form.errors.email" />
      </div>

      <div>
        <InputLabel for="password" value="Password" />
        <TextInput
          id="password"
          v-model="form.password"
          type="password"
          class="mt-1 block w-full"
          autocomplete="new-password"
          required
        />
        <p class="mt-1 text-xs text-gray-500">
          At least 8 characters; include letters and numbers.
        </p>
        <InputError class="mt-2" :message="form.errors.password" />
      </div>

      <div>
        <InputLabel for="password_confirmation" value="Confirm Password" />
        <TextInput
          id="password_confirmation"
          v-model="form.password_confirmation"
          type="password"
          class="mt-1 block w-full"
          autocomplete="new-password"
          required
        />
        <InputError class="mt-2" :message="form.errors.password_confirmation" />
      </div>

      <!-- Role selection -->
      <div>
        <InputLabel for="role" value="Role" />
        <select
          id="role"
          v-model="form.role"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          required
        >
          <option value="student">Student</option>
          <option value="instructor">Instructor</option>
        </select>
        <InputError class="mt-2" :message="form.errors.role" />
      </div>

      <div class="flex items-center justify-between">
        <Link
          :href="route('login')"
          class="text-sm text-gray-600 hover:text-gray-900 underline"
        >
          Already registered? Log in
        </Link>

        <PrimaryButton :disabled="form.processing">
          Register
        </PrimaryButton>
      </div>
    </form>
  </GuestLayout>
</template>
