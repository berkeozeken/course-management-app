<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CourseController extends Controller
{
    public function index()
    {
        $q = Course::with('instructor:id,name')->latest('id');

        // student sadece yayında olanları görsün
        if ($this->hasRole(['student'])) {
            $q->where('is_published', true);
        }

        $courses = $q->paginate(12);

        return Inertia::render('Courses/Index', ['courses' => $courses]);
    }

    public function create()
    {
        $this->authorizeByRole(); // admin/instructor
        return Inertia::render('Courses/Create');
    }

    public function store(Request $request)
    {
        $this->authorizeByRole();

        $data = $request->validate([
            'title'        => ['required','string','max:255'],
            'description'  => ['nullable','string'],
            'price'        => ['nullable','numeric','min:0'],
            'cover_url'    => ['nullable','url','max:2048'],
            'is_published' => ['boolean'],
        ]);

        $course = Course::create([
            'title'         => $data['title'],
            'slug'          => Str::slug($data['title']).'-'.Str::lower(Str::random(6)),
            'description'   => $data['description'] ?? null,
            'price'         => $data['price'] ?? null,
            'cover_url'     => $data['cover_url'] ?? null,
            'is_published'  => $data['is_published'] ?? false,
            'instructor_id' => Auth::id(),
        ]);

        return redirect()->route('courses.show', $course->id)->with('success','Kurs oluşturuldu.');
    }

    public function show(Course $course)
    {
        $course->load(['instructor:id,name','sections.lessons']);

        $user   = Auth::user();
        $userId = Auth::id();
        $role   = strtolower($user->role ?? '');

        // student taslak kursu görmesin
        if ($role === 'student' && !$course->is_published) {
            abort(404);
        }

        // kayıt kontrolü (modelde hangi ilişki varsa)
        $isEnrolled = false;
        if ($userId) {
            if (method_exists($course, 'isEnrolledBy')) {
                $isEnrolled = $course->isEnrolledBy($userId);
            } elseif (method_exists($course, 'students')) {
                $isEnrolled = $course->students()->where('user_id', $userId)->exists();
            } elseif (method_exists($course, 'enrollments')) {
                $isEnrolled = $course->enrollments()->where('user_id', $userId)->exists();
            }
        }

        // yönetim yetkisi: admin veya kursun eğitmeni
        $canManage = $this->hasRole(['admin','instructor']) &&
                     ($role === 'admin' || $course->instructor_id === $userId);

        // ===== Eğitmen için katılımcılar (sayı + ilk 10) =====
        $participants = null;
        if ($canManage) {
            if (method_exists($course, 'students')) {
                $all = $course->students()
                    ->select('users.id','users.name','users.email')
                    ->withPivot('created_at')
                    ->orderBy('users.name')
                    ->get();
            } else {
                $all = $course->enrollments()
                    ->with('user:id,name,email')
                    ->get()
                    ->map(fn($e) => (object)[
                        'id'         => $e->user->id,
                        'name'       => $e->user->name,
                        'email'      => $e->user->email,
                        'created_at' => $e->created_at,
                    ]);
            }

            $participants = [
                'count'    => $all->count(),
                'sample'   => $all->take(10)->values(),
                'has_more' => $all->count() > 10,
            ];
        }

        return Inertia::render('Courses/Show', [
            'course' => $course,
            'meta'   => [
                'is_published' => (bool) $course->is_published,
                'enrolled'     => $isEnrolled,
            ],
            'can' => [
                'update'         => $canManage,
                'manageSections' => $canManage,
                'createLesson'   => $canManage,
                'enrollControls' => $role === 'student'
                                    && $course->is_published
                                    && $course->instructor_id !== $userId,
            ],
            'participants' => $participants,
        ]);
    }

    public function edit(Course $course)
    {
        $this->authorizeByRole();
        return Inertia::render('Courses/Edit', ['course' => $course]);
    }

    public function update(Request $request, Course $course)
    {
        $this->authorizeByRole();

        $data = $request->validate([
            'title'        => ['required','string','max:255'],
            'description'  => ['nullable','string'],
            'price'        => ['nullable','numeric','min:0'],
            'cover_url'    => ['nullable','url','max:2048'],
            'is_published' => ['boolean'],
        ]);

        $course->update($data);

        return redirect()->route('courses.show', $course)->with('success','Kurs güncellendi.');
    }

    public function togglePublish(Course $course)
    {
        $this->authorizeByRole();

        $course->update(['is_published' => !$course->is_published]);

        return back()->with('success', $course->is_published ? 'Kurs yayınlandı.' : 'Kurs yayından kaldırıldı.');
    }

    /**
     * Eğitmen için tam katılımcı listesi (JSON)
     */
    public function participants(Request $request, Course $course)
    {
        // sadece admin veya kursun eğitmeni
        abort_unless(
            ($this->hasRole(['admin']) || ( $this->hasRole(['instructor']) && Auth::id() === $course->instructor_id )),
            403
        );

        $perPage = (int) $request->integer('per_page', 20);

        if (method_exists($course, 'students')) {
            $paginator = $course->students()
                ->select('users.id','users.name','users.email')
                ->withPivot('created_at')
                ->orderBy('users.name')
                ->paginate($perPage);
            $items = $paginator->map(fn($u) => [
                'id'        => $u->id,
                'name'      => $u->name,
                'email'     => $u->email,
                'joined_at' => optional($u->pivot?->created_at)->toDateTimeString(),
            ]);
        } else {
            $paginator = $course->enrollments()
                ->with('user:id,name,email')
                ->orderBy('created_at','desc')
                ->paginate($perPage);
            $items = $paginator->map(fn($e) => [
                'id'        => $e->user->id,
                'name'      => $e->user->name,
                'email'     => $e->user->email,
                'joined_at' => optional($e->created_at)->toDateTimeString(),
            ]);
        }

        return response()->json([
            'data'       => $items,
            'total'      => $paginator->total(),
            'per_page'   => $paginator->perPage(),
            'current'    => $paginator->currentPage(),
            'last_page'  => $paginator->lastPage(),
        ]);
    }

    // ===== helpers =====
    private function hasRole(array $roles): bool
    {
        $user = Auth::user();
        if (!$user) return false;

        $userRole = strtolower($user->role ?? '');
        $roles    = array_map('strtolower', $roles);

        return in_array($userRole, $roles, true);
    }

    private function authorizeByRole(): void
    {
        abort_unless($this->hasRole(['admin','instructor']), 403);
    }
}
