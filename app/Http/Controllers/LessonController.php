<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LessonController extends Controller
{
    public function store(Request $r)
    {
        // Hem section_id hem course_id destekli
        $data = $r->validate([
            'section_id' => ['sometimes','nullable','exists:sections,id'],
            'course_id'  => ['sometimes','nullable','exists:courses,id'],
            'title'      => ['required','string','max:255'],
            'content'    => ['nullable','string'],
            'position'   => ['nullable','integer','min:1'],
            'is_free'    => ['sometimes','boolean'],
        ]);

        // En az birinden biri gelmeli
        if (empty($data['section_id']) && empty($data['course_id'])) {
            throw ValidationException::withMessages([
                'section_id' => 'section_id veya course_id zorunlu.',
            ]);
        }

        // Hedef section'ı belirle/oluştur
        if (!empty($data['section_id'])) {
            $sectionId = $data['section_id'];
        } else {
            // course_id verildiyse o kursun ilk section'ını bul; yoksa oluştur
            $section = Section::where('course_id', $data['course_id'])
                ->orderBy('position')
                ->first();

            if (!$section) {
                $section = Section::create([
                    'course_id' => $data['course_id'],
                    'title'     => 'General',
                    'position'  => 1,
                ]);
            }
            $sectionId = $section->id;
        }

        $lesson = Lesson::create([
            'section_id' => $sectionId,
            'title'      => $data['title'],
            'content'    => $data['content'] ?? null,
            'position'   => $data['position'] ?? 1,
            'is_free'    => (bool)($data['is_free'] ?? false),
        ]);

        return response()->json($lesson, 201);
    }

    public function update(Request $r, Lesson $lesson)
    {
        $data = $r->validate([
            'title'    => ['required','string','max:255'],
            'content'  => ['nullable','string'],
            'position' => ['nullable','integer','min:1'],
            'is_free'  => ['sometimes','boolean'],
        ]);

        $lesson->update([
            'title'    => $data['title'],
            'content'  => $data['content'] ?? $lesson->content,
            'position' => $data['position'] ?? $lesson->position,
            'is_free'  => array_key_exists('is_free',$data) ? (bool)$data['is_free'] : $lesson->is_free,
        ]);

        return response()->json($lesson);
    }

    public function destroy(Lesson $lesson)
    {
        $lesson->delete();
        return response()->noContent();
    }
}
