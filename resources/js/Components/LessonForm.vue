<script setup>
import { reactive } from 'vue'
import axios from 'axios'

const props = defineProps({
  // İkisi de opsiyonel görünüyor; en az birini parent verecek.
  courseId: { type: Number, default: null },
  sectionId: { type: Number, default: null },
})

const form = reactive({
  title: '',
  content: '',
  position: 1,
  is_free: false,
})

async function save() {
  try {
    const payload = {
      title: form.title,
      content: form.content || null,
      position: form.position || 1,
      is_free: !!form.is_free,
    }
    if (props.sectionId) payload.section_id = props.sectionId
    else if (props.courseId) payload.course_id = props.courseId

    await axios.post('/lessons', payload) // web route (session auth)

    // reset
    form.title = ''
    form.content = ''
    form.position = 1
    form.is_free = false
    alert('Lesson created')

    // Basit yöntem: sayfayı yenile ki liste güncellensin
    window.location.reload()
  } catch (e) {
    console.error(e)
    alert('Ders eklenemedi.')
  }
}
</script>

<template>
  <div class="p-4 border rounded space-y-2">
    <h3 class="font-semibold">Add Lesson</h3>
    <input v-model="form.title" placeholder="Title" class="border p-2 w-full" />
    <textarea v-model="form.content" placeholder="Content" class="border p-2 w-full" />
    <input type="number" min="1" v-model.number="form.position" class="border p-2 w-full" />
    <label class="inline-flex items-center gap-2">
      <input type="checkbox" v-model="form.is_free" /> Free preview
    </label>
    <button @click="save" class="bg-black text-white px-3 py-2 rounded">Create</button>
  </div>
</template>
