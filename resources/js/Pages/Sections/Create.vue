<script setup>
import { useForm, Link } from '@inertiajs/vue3'

const props = defineProps({ course: Object })

const form = useForm({
  title: '',
  position: null,
})

const submit = () => {
  form.post(`/courses/${props.course.id}/sections`, { preserveScroll: true })
}
</script>

<template>
  <div style="max-width: 720px; margin: 24px auto; padding: 16px; border: 1px solid #e5e7eb; border-radius: 12px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
      <h1>Yeni Bölüm ({{ course?.title }})</h1>
      <Link :href="`/courses/${course.id}`">Geri</Link>
    </div>

    <div style="display:flex; flex-direction:column; gap:12px;">
      <div>
        <label>Başlık</label>
        <input v-model="form.title" type="text" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:8px;" />
        <div v-if="form.errors.title" style="color:#dc2626; font-size:12px;">{{ form.errors.title }}</div>
      </div>

      <div>
        <label>Sıra (opsiyonel)</label>
        <input v-model.number="form.position" type="number" min="1" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:8px;" />
      </div>

      <div style="display:flex; gap:8px;">
        <button @click="submit" :disabled="form.processing" style="padding:8px 14px; border:1px solid #000; background:#000; color:#fff; border-radius:8px;">
          Kaydet
        </button>
        <Link :href="`/courses/${course.id}`" style="padding:8px 14px; border:1px solid #ddd; border-radius:8px;">Vazgeç</Link>
      </div>
    </div>
  </div>
</template>
