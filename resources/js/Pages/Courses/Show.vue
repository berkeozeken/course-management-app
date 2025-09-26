<script setup>
import { useForm, Link } from '@inertiajs/vue3'

const props = defineProps({
  course: Object,
  canManage: Boolean,
})

// Section form (zaten var)
const sectionForm = useForm({ title: '' })
const addSection = () => {
  sectionForm.post(route('sections.store', props.course.slug), {
    onSuccess: () => sectionForm.reset('title')
  })
}

// Lesson form(lar): her section için ayrı state
const lessonForms = Object.fromEntries(
  (props.course.sections || []).map(s => [s.id, { title:'', is_free:false, video_url:'' }])
)

function submitLesson(sectionId) {
  const f = lessonForms[sectionId]
  useForm({ ...f }).post(route('lessons.store', sectionId), {
    onSuccess: () => {
      lessonForms[sectionId].title = ''
      lessonForms[sectionId].is_free = false
      lessonForms[sectionId].video_url = ''
    }
  })
}
</script>

<template>
  <div class="p-6 space-y-6">
    <div>
      <h1 class="text-2xl font-bold">{{ course.title }}</h1>
      <p v-if="course.description" class="opacity-80">{{ course.description }}</p>
    </div>

    <!-- Add Section -->
    <div v-if="canManage" class="p-4 border rounded max-w-xl">
      <h2 class="font-semibold mb-2">Add Section</h2>
      <div class="flex gap-2">
        <input v-model="sectionForm.title" type="text" class="border rounded p-2 flex-1" placeholder="Section title" />
        <button @click="addSection" :disabled="sectionForm.processing || !sectionForm.title" class="border rounded px-4">
          Add
        </button>
      </div>
      <div v-if="sectionForm.errors.title" class="text-red-600 text-sm mt-1">{{ sectionForm.errors.title }}</div>
    </div>

    <!-- Sections + Lessons -->
    <div>
      <h2 class="text-xl font-semibold mb-2">Sections</h2>

      <div v-for="s in course.sections" :key="s.id" class="mb-6">
        <div class="font-semibold mb-1">{{ s.title }}</div>

        <!-- Add Lesson (sadece yetkili) -->
        <div v-if="canManage" class="p-3 border rounded max-w-2xl mb-3">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
            <input v-model="lessonForms[s.id].title" type="text" class="border rounded p-2 col-span-2" placeholder="Lesson title" />
            <input v-model="lessonForms[s.id].video_url" type="url" class="border rounded p-2 col-span-2" placeholder="Video URL (optional)" />
            <label class="flex items-center gap-2 text-sm">
              <input type="checkbox" v-model="lessonForms[s.id].is_free" />
              Free
            </label>
            <button
              @click="submitLesson(s.id)"
              class="border rounded px-4 py-2"
              :disabled="!lessonForms[s.id].title">
              Add Lesson
            </button>
          </div>
        </div>

        <ul class="list-disc ml-6">
          <li v-for="l in s.lessons" :key="l.id">
            {{ l.title }}
            <span v-if="l.is_free" class="text-xs opacity-70">(free)</span>
          </li>
        </ul>
      </div>
    </div>

    <div class="pt-4">
      <Link :href="route('courses.index')" class="underline">← Back to Courses</Link>
    </div>
  </div>
</template>
