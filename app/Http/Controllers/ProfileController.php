<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman profile
     */
    public function index()
    {
        $user = Auth::user();
        return view('layouts.profile', compact('user'));
    }

    /**
     * Update profile (nama dan no telepon)
     */
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'nama_user' => 'required|string|max:50',
            'no_telepon' => 'required|string|max:15|regex:/^[0-9]+$/',
            'email'      => 'nullable|email|max:191|unique:users,email,' . $user->id_user . ',id_user',
        ], [
            'nama_user.required' => 'Nama wajib diisi',
            'nama_user.max' => 'Nama maksimal 50 karakter',
            'no_telepon.required' => 'No telepon wajib diisi',
            'no_telepon.max' => 'No telepon maksimal 15 karakter',
            'no_telepon.regex' => 'No telepon hanya boleh berisi angka',
            'email.email'         => 'Format email tidak valid',
            'email.unique'        => 'Email sudah digunakan',
        ]);

        $user->update([
            'nama_user' => $validated['nama_user'],
            'no_telepon' => $validated['no_telepon'],
            'email'      => $validated['email'] ?? null,
        ]);

        return redirect()->route('profile.index')
            ->with('success', 'Profile berhasil diperbarui');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(4)],
        ], [
            'current_password.required' => 'Password lama wajib diisi',
            'password.required' => 'Password baru wajib diisi',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'password.min' => 'Password minimal 4 karakter',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Cek password lama
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak sesuai']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profile.index')
            ->with('success', 'Password berhasil diperbarui');
    }

    
}