<script setup>
import { Link } from '@inertiajs/vue3'
defineProps({ href: { type: String, required: true } })
</script>

<template>
  <Link :href="href" class="inline-flex items-center rounded-lg border px-3 py-1.5 text-sm hover:bg-gray-50">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
      <path fill-rule="evenodd" d="M12.293 16.293a1 1 0 010 1.414l-.083.073a1 1 0 01-1.32-.083l-6-6-.073-.082a1 1 0 01.073-1.32l6-6 .094-.083a1 1 0 011.414 1.414L7.414 10l4.879 4.879a1 1 0 010 1.414z" clip-rule="evenodd"/>
    </svg>
    <slot>Back</slot>
  </Link>
</template>
