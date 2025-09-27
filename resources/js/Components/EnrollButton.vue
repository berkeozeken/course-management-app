<script setup>
import { ref } from 'vue'
import axios from 'axios'

const props = defineProps({
  courseId: { type: Number, required: true },
  initialStatus: { type: String, default: 'none' } // 'active' | 'none'
})

const status = ref(props.initialStatus)

async function enroll() {
  try {
    await axios.post('/enrollments', { course_id: props.courseId }) // web route (session auth)
    status.value = 'active'
  } catch (e) {
    console.error(e)
    alert('Kayıt sırasında bir hata oluştu.')
  }
}
</script>

<template>
  <button
    v-if="status !== 'active'"
    @click="enroll"
    class="bg-black text-white px-3 py-2 rounded"
  >
    Kursa Kaydol
  </button>
  <span v-else class="px-3 py-2 border rounded">Kaydolundu</span>
</template>
