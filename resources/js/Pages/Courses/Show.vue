<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import EnrollButton from '@/Components/EnrollButton.vue'
import { computed, ref } from 'vue'

const props = defineProps({
  course:        { type: Object, required: true },
  meta:          { type: Object, required: true }, // { is_published, enrolled }
  can:           { type: Object, required: true }, // { update, manageSections, createLesson, enrollControls }
  participants:  { type: Object, default: null },  // { count, sample[], has_more } (sadece eğitmen)
})

const coverUrl = props.course?.cover_url || null
const isFree   = !props.course?.price || Number(props.course?.price) === 0
const participantsProp = props.participants ?? null

// İlk ders id'si
const firstLessonId = computed(() => {
  const sections = props.course?.sections || []
  for (const s of sections) if (s?.lessons?.length) return s.lessons[0].id
  return null
})

// admin/instructor eylemleri
const togglePublish = () => {
  router.post(route('courses.togglePublish', props.course.id), {}, { preserveScroll: true })
}

// silme
const destroySection = (id) => {
  if (!confirm('Bu bölümü silmek istediğine emin misin? Bu işlem geri alınamaz.')) return
  router.delete(route('sections.destroy', id), { preserveScroll: true })
}
const destroyLesson = (id) => {
  if (!confirm('Dersi silmek istediğine emin misin?')) return
  router.delete(route('lessons.destroy', id), { preserveScroll: true })
}

// ===== Katılımcı Modal State =====
const participantsOpen = ref(false)
const participants = ref({ loading: false, rows: [], total: 0, page: 1, per_page: 20, last_page: 1 })

const openParticipants = async (page = 1) => {
  participantsOpen.value = true
  participants.value.loading = true
  try {
    const url = route('courses.participants', props.course.id) + `?page=${page}&per_page=${participants.value.per_page || 20}`
    const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    const json = await res.json()
    participants.value.rows = json.data
    participants.value.total = json.total
    participants.value.page = json.current
    participants.value.per_page = json.per_page
    participants.value.last_page = json.last_page
  } catch (e) {
    console.error(e)
  } finally {
    participants.value.loading = false
  }
}
</script>

<template>
  <Head :title="course.title" />

  <AuthenticatedLayout>
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 py-6 space-y-6">

      <!-- Kapak (varsa) -->
      <div v-if="coverUrl" class="rounded-2xl overflow-hidden shadow bg-slate-900/5">
        <div class="relative w-full" style="aspect-ratio:16/9;">
          <img :src="coverUrl" alt="Course cover" class="absolute inset-0 h-full w-full object-cover" />
        </div>
      </div>

      <!-- Başlık -->
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="min-w-0">
          <div class="flex items-center gap-3">
            <h1 class="truncate text-2xl font-semibold text-gray-900">{{ course.title }}</h1>
            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                  :class="meta.is_published ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'">
              {{ meta.is_published ? 'Yayında' : 'Taslak' }}
            </span>
            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-gray-100 text-gray-700">
              {{ isFree ? 'Ücretsiz' : ('₺' + Number(course.price).toLocaleString('tr-TR')) }}
            </span>
          </div>
          <p class="mt-1 text-sm text-gray-500">
            Eğitmen: <span class="font-medium text-gray-700">{{ course.instructor?.name ?? '—' }}</span>
          </p>
        </div>

        <!-- admin/instructor aksiyonları -->
        <div v-if="can.update" class="flex items-center gap-2">
          <Link :href="route('courses.edit', course.id)"
                class="rounded-xl border px-3 py-1.5 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            Düzenle
          </Link>
          <button type="button"
                  @click="togglePublish"
                  class="rounded-xl px-3 py-1.5 text-sm font-medium text-white"
                  :class="meta.is_published ? 'bg-amber-600 hover:bg-amber-700' : 'bg-emerald-600 hover:bg-emerald-700'">
            {{ meta.is_published ? 'Yayından Kaldır' : 'Yayınla' }}
          </button>
        </div>
      </div>

      <!-- Grid -->
      <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Sol -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Açıklama -->
          <div class="rounded-2xl bg-white p-6 shadow">
            <h2 class="mb-3 text-lg font-semibold text-gray-900">Açıklama</h2>
            <p class="whitespace-pre-line break-words text-gray-700">
              {{ course.description || 'Bu kurs için açıklama eklenmemiş.' }}
            </p>
          </div>

          <!-- Bölümler & Dersler -->
          <div class="rounded-2xl bg-white p-6 shadow">
            <div class="mb-4 flex items-center justify-between">
              <h2 class="text-lg font-semibold text-gray-900">Bölümler & Dersler</h2>

              <Link v-if="can.manageSections"
                    :href="route('sections.create', course.id)"
                    class="rounded-lg bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-700">
                + Bölüm Ekle
              </Link>
            </div>

            <template v-if="course.sections?.length">
              <div v-for="section in course.sections" :key="section.id" class="mb-4 rounded-2xl border border-gray-200 overflow-hidden">
                <div class="flex items-center justify-between px-4 py-3 bg-gray-50">
                  <h3 class="font-medium text-gray-800">{{ section.title }}</h3>

                  <div v-if="can.manageSections" class="flex items-center gap-2">
                    <Link :href="route('lessons.create', section.id)"
                          class="rounded-lg border px-3 py-1.5 text-xs font-medium text-gray-700 bg-white hover:bg-gray-50">+ Ders Ekle</Link>
                    <Link :href="route('sections.edit', section.id)"
                          class="rounded-lg border px-3 py-1.5 text-xs font-medium text-gray-700 bg-white hover:bg-gray-50">Bölümü Düzenle</Link>
                    <button type="button" @click="destroySection(section.id)"
                            class="rounded-lg px-3 py-1.5 text-xs font-medium text-white bg-rose-600 hover:bg-rose-700">Sil</button>
                  </div>
                </div>

                <div class="px-4 py-3">
                  <template v-if="section.lessons?.length">
                    <ul class="space-y-2">
                      <li v-for="lesson in section.lessons" :key="lesson.id"
                          class="flex items-center justify-between rounded-lg border border-gray-200 px-3 py-2">
                        <span class="text-sm text-gray-800">• {{ lesson.title }}</span>

                        <div class="flex items-center gap-2">
                          <!-- ÖĞRENCİ: İzle -->
                          <template v-if="!can.createLesson">
                            <Link v-if="meta.enrolled"
                                  :href="route('lessons.show', lesson.id)"
                                  class="rounded-lg bg-indigo-600 px-2.5 py-1 text-xs font-medium text-white hover:bg-indigo-700"
                                  title="Dersi izle">İzle</Link>
                            <button v-else
                                    class="rounded-lg bg-gray-200 px-2.5 py-1 text-xs text-gray-600 cursor-not-allowed"
                                    title="Dersi izlemek için kayıt olmalısın" disabled>İzle</button>
                          </template>

                          <!-- EĞİTMEN/ADMIN: Düzenle / Sil -->
                          <template v-if="can.createLesson">
                            <Link :href="route('lessons.edit', lesson.id)"
                                  class="rounded-lg border px-2.5 py-1 text-xs text-gray-700 bg-white hover:bg-gray-50">Düzenle</Link>
                            <button type="button" @click="destroyLesson(lesson.id)"
                                    class="rounded-lg px-2.5 py-1 text-xs text-white bg-rose-600 hover:bg-rose-700">Sil</button>
                          </template>
                        </div>
                      </li>
                    </ul>
                  </template>
                  <p v-else class="text-sm text-gray-500">Bu bölümde henüz ders yok.</p>
                </div>
              </div>
            </template>

            <p v-else class="text-gray-500">Henüz bölüm eklenmemiş.</p>
          </div>
        </div>

        <!-- Sağ -->
        <aside class="lg:col-span-1">
          <div class="sticky top-20 space-y-6">
            <!-- Genel Bakış -->
            <div class="rounded-2xl bg-white p-6 shadow">
              <h3 class="mb-3 text-base font-semibold text-gray-900">Kursa Genel Bakış</h3>
              <dl class="grid grid-cols-2 gap-3 text-sm">
                <div>
                  <dt class="text-gray-500">Durum</dt>
                  <dd class="font-medium" :class="meta.is_published ? 'text-emerald-700' : 'text-amber-700'">
                    {{ meta.is_published ? 'Yayında' : 'Taslak' }}
                  </dd>
                </div>
                <div>
                  <dt class="text-gray-500">Ücret</dt>
                  <dd class="font-medium">{{ isFree ? 'Ücretsiz' : ('₺' + Number(course.price).toLocaleString('tr-TR')) }}</dd>
                </div>
                <div class="col-span-2">
                  <dt class="text-gray-500">Eğitmen</dt>
                  <dd class="font-medium text-gray-800">{{ course.instructor?.name ?? '—' }}</dd>
                </div>
              </dl>

              <!-- STUDENT CTA -->
              <div class="mt-5">
                <div v-if="can.enrollControls">
                  <EnrollButton :course-id="course.id" :is-published="meta.is_published" :enrolled="meta.enrolled" />
                </div>

                <div v-if="meta.enrolled && firstLessonId" class="space-y-2 mt-3">
                  <Link :href="route('lessons.show', firstLessonId)"
                        class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 w-full">
                    Derse Başla
                  </Link>
                  <p class="text-xs text-gray-500">İlk dersten başlayacaksın.</p>
                </div>

                <p v-if="!meta.is_published" class="mt-2 text-xs text-amber-700">
                  Bu kurs taslak durumunda; kayıt açılamaz.
                </p>
              </div>
            </div>

            <!-- EĞİTMEN: Katılımcılar -->
            <div v-if="can.update" class="rounded-2xl bg-white p-6 shadow">
              <h3 class="mb-3 text-base font-semibold text-gray-900">Katılımcılar</h3>
              <p class="text-sm text-gray-600">
                Toplam <span class="font-semibold text-gray-900">{{ participantsProp?.count ?? 0 }}</span> öğrenci.
              </p>

              <ul v-if="participantsProp?.sample?.length" class="mt-3 space-y-1 text-sm text-gray-700">
                <li v-for="s in participantsProp.sample" :key="s.id">
                  • {{ s.name }} <span class="text-gray-500">({{ s.email }})</span>
                </li>
              </ul>

              <button class="mt-4 w-full rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                      @click="openParticipants(1)">
                Tüm Listeyi Gör
              </button>
            </div>
          </div>
        </aside>
      </div>
    </div>

    <!-- Participants Modal -->
    <div v-if="participantsOpen" class="fixed inset-0 z-50 flex items-center justify-center">
      <div class="absolute inset-0 bg-black/40" @click="participantsOpen=false"></div>

      <div class="relative z-10 w-full max-w-2xl rounded-2xl bg-white shadow-xl">
        <div class="flex items-center justify-between border-b px-5 py-3">
          <h3 class="text-base font-semibold">Kursa Kayıtlı Öğrenciler</h3>
          <button class="text-gray-500 hover:text-gray-800" @click="participantsOpen=false">✕</button>
        </div>

        <div class="max-h-[60vh] overflow-y-auto px-5 py-4">
          <div v-if="participants.loading" class="py-10 text-center text-gray-500">Yükleniyor…</div>

          <template v-else>
            <table class="w-full text-sm">
              <thead>
                <tr class="text-left text-gray-500 border-b">
                  <th class="py-2 pr-3">Ad Soyad</th>
                  <th class="py-2 pr-3">E-posta</th>
                  <th class="py-2">Kayıt Tarihi</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="r in participants.rows" :key="r.id" class="border-b last:border-0">
                  <td class="py-2 pr-3 font-medium text-gray-800">{{ r.name }}</td>
                  <td class="py-2 pr-3 text-gray-700">{{ r.email }}</td>
                  <td class="py-2 text-gray-700">{{ r.joined_at ?? '—' }}</td>
                </tr>
              </tbody>
            </table>

            <div class="mt-4 flex items-center justify-between text-sm">
              <span class="text-gray-600">Toplam {{ participants.total }} kayıt</span>
              <div class="space-x-2">
                <button class="rounded border px-3 py-1 disabled:opacity-50"
                        :disabled="participants.page <= 1"
                        @click="openParticipants(participants.page - 1)">Önceki</button>
                <button class="rounded border px-3 py-1 disabled:opacity-50"
                        :disabled="participants.page >= participants.last_page"
                        @click="openParticipants(participants.page + 1)">Sonraki</button>
              </div>
            </div>
          </template>
        </div>

        <div class="border-t px-5 py-3 flex justify-end">
          <button class="rounded-lg border px-3 py-1.5 text-sm text-gray-700 bg-white hover:bg-gray-50"
                  @click="participantsOpen=false">Kapat</button>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
