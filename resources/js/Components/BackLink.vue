<script setup>
const props = defineProps({
  href: { type: String, default: null }, // verilirse Link ile gideriz
})
function goBack() {
  if (window.history.length > 1) window.history.back()
  else window.location.href = props.href ?? '/courses'
}
</script>

<template>
  <button @click="goBack" class="text-sm underline">&larr; Back</button>
</template>
