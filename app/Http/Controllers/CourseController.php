<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::withCount('enrollments')->latest()->get();

        return Inertia::render('Courses/Index', [
            'courses' => $courses,
        ]);
    }

    public function show(Request $request, $slug)
    {
        $course = Course::where('slug', $slug)
            ->with(['sections.lessons', 'owner'])
            ->firstOrFail();

        $canManage = $request->user()?->can('update', $course) ?? false;

        // öğrenci kayıt durumu
        $status = 'none';
        if ($request->user()) {
            $status = \App\Models\Enrollment::where('user_id', $request->user()->id)
                ->where('course_id', $course->id)
                ->where('status', 'active')
                ->exists() ? 'active' : 'none';
        }

        return Inertia::render('Courses/Show', [
            'course'            => $course,
            'canManage'         => $canManage,
            'enrollmentStatus'  => $status,
        ]);
    }

    public function create()
    {
        $this->authorize('create', Course::class);
        return Inertia::render('Courses/Create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Course::class);

        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'slug'        => 'required|string|max:255|unique:courses,slug',
            'description' => 'nullable|string',
            'status'      => 'required|in:draft,published',
        ]);

        $data['owner_id'] = $request->user()->id;

        $course = Course::create($data);

        return redirect()->route('courses.show', $course->slug);
    }

    public function edit($slug)
    {
        $course = Course::where('slug', $slug)->firstOrFail();
        $this->authorize('update', $course);

        return Inertia::render('Courses/Edit', ['course' => $course]);
    }

    public function update(Request $request, $slug)
    {
        $course = Course::where('slug', $slug)->firstOrFail();
        $this->authorize('update', $course);

        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'slug'        => 'required|string|max:255|unique:courses,slug,' . $course->id,
            'description' => 'nullable|string',
            'status'      => 'required|in:draft,published',
        ]);

        $course->update($data);

        return redirect()->route('courses.show', $course->slug);
    }

    public function destroy($slug)
    {
        $course = Course::where('slug', $slug)->firstOrFail();
        $this->authorize('delete', $course);

        $course->delete();

        return redirect()->route('courses.index');
    }
}
