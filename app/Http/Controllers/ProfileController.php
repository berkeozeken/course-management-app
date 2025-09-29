<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Profile ana sayfası (görüntüleme)
     */
    public function show(): Response
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return Inertia::render('Profile/Index', [
            'auth' => [
                'user' => $user?->only(['id', 'name', 'email', 'role']),
            ],
            'flash' => [
                'success' => session('success'),
            ],
        ]);
    }

    /**
     * Hesap bilgilerini güncelle (isim / email)
     */
    public function updateAccount(Request $request)
    {
        $data = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'string', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore(Auth::id()),
            ],
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->update($data);

        return back()->with('success', 'Profile updated.');
    }

    /**
     * Parola güncelle
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password changed.');
    }
}
