<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    // Kullanıcının kayıtları
    public function index(Request $req)
    {
        $items = Enrollment::with('course:id,title')
            ->where('user_id', $req->user()->id)
            ->latest()
            ->get();

        return response()->json($items);
    }

    // Kursa kaydol
    public function store(Request $req)
    {
        $data = $req->validate([
            'course_id' => ['required','exists:courses,id'],
        ]);

        $enrollment = Enrollment::firstOrCreate(
            ['user_id' => $req->user()->id, 'course_id' => $data['course_id']],
            ['status' => 'active']
        );

        // Daha önce iptal edildiyse tekrar aktif et
        if ($enrollment->status !== 'active') {
            $enrollment->update(['status' => 'active']);
        }

        return response()->json($enrollment, 201);
    }

    // Kaydı iptal (soft cancel: status=cancelled)
    public function destroy(Request $req, Enrollment $enrollment)
    {
        abort_if($enrollment->user_id !== $req->user()->id, 403, 'Forbidden');

        $enrollment->update(['status' => 'cancelled']);
        return response()->noContent();
    }
}
