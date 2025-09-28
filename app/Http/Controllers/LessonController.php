<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LessonController extends Controller
{
    private function ensureInstructor(): void
    {
        $role = strtolower(Auth::user()->role ?? '');
        abort_unless(in_array($role, ['admin','instructor'], true), 403);
    }

    public function create(Section $section)
    {
        $this->ensureInstructor();
        $section->load('course:id,title');
        return Inertia::render('Lessons/Create', ['section' => $section]);
    }

    public function store(Request $request, Section $section)
    {
        $this->ensureInstructor();

        $data = $request->validate([
            'title'            => ['required','string','max:255'],
            'content'          => ['nullable','string'],
            'video_url'        => ['nullable','url','max:2048'],
            'duration_minutes' => ['nullable','integer','min:0'],
            'is_free'          => ['boolean'],
        ]);

        $position = (int) ($section->lessons()->max('position') + 1);

        Lesson::create(array_merge($data, [
            'section_id' => $section->id,
            'position'   => $position,
        ]));

        return redirect()->route('courses.show', $section->course_id)->with('success', 'Ders eklendi.');
    }

    public function show(Lesson $lesson)
    {
        $lesson->load('section.course.instructor:id,name');

        $userId      = Auth::id();
        $course      = $lesson->section->course;
        $isInstructor= $userId && $course->instructor_id === $userId;
        $isEnrolled  = $course->isEnrolledBy($userId);
        $canWatch    = $isInstructor || $isEnrolled || $lesson->is_free;

        // Kursun tüm derslerini sıralı tek listeye aç
        $course->load([
            'sections' => fn($q) => $q->orderBy('id'),
            'sections.lessons' => fn($q) => $q->orderBy('position')->orderBy('id'),
        ]);

        $flat = [];
        foreach ($course->sections as $s) {
            foreach ($s->lessons as $ls) {
                $flat[] = [
                    'id'      => $ls->id,
                    'title'   => $ls->title,
                    'section' => ['id' => $s->id, 'title' => $s->title],
                ];
            }
        }

        $idx    = array_search($lesson->id, array_column($flat, 'id'), true);
        $prevId = $idx !== false && $idx > 0 ? $flat[$idx - 1]['id'] : null;
        $nextId = $idx !== false && $idx < count($flat) - 1 ? $flat[$idx + 1]['id'] : null;

        return Inertia::render('Lessons/Show', [
            'lesson' => [
                'id'               => $lesson->id,
                'title'            => $lesson->title,
                'content'          => $lesson->content,
                'video_url'        => $lesson->video_url,
                'duration_minutes' => $lesson->duration_minutes,
                'is_free'          => (bool) $lesson->is_free,
                'section'          => ['id' => $lesson->section->id, 'title' => $lesson->section->title],
            ],
            'course' => [
                'id'    => $course->id,
                'title' => $course->title,
            ],
            'nav'      => ['prev' => $prevId, 'next' => $nextId],
            'is_last'  => $nextId === null,   // <<< SON DERS Mİ?
            'canWatch' => $canWatch,
            'canEdit'  => $isInstructor,
        ]);
    }

    public function edit(Lesson $lesson)
    {
        $this->ensureInstructor();
        $lesson->load('section.course:id,title');
        return Inertia::render('Lessons/Edit', ['lesson' => $lesson]);
    }

    public function update(Request $request, Lesson $lesson)
    {
        $this->ensureInstructor();

        $data = $request->validate([
            'title'            => ['required','string','max:255'],
            'content'          => ['nullable','string'],
            'video_url'        => ['nullable','url','max:2048'],
            'duration_minutes' => ['nullable','integer','min:0'],
            'is_free'          => ['boolean'],
        ]);

        $lesson->update($data);

        return redirect()->route('courses.show', $lesson->section->course_id)->with('success','Ders güncellendi.');
    }

    public function destroy(Lesson $lesson)
    {
        $this->ensureInstructor();
        $courseId = $lesson->section->course_id;
        $lesson->delete();

        return redirect()->route('courses.show', $courseId)->with('success','Ders silindi.');
    }
}
