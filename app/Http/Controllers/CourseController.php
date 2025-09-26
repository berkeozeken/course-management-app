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

        return Inertia::render('Courses/Show', [
            'course'    => $course,
            'canManage' => $canManage,
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
}
