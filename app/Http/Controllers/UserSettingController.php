<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class UserSettingController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index()
    {
        $allowed = [20, 40, 60, 80, 99];

        $perPage = (int) request('per_page', 20);
        if (!in_array($perPage, $allowed)) {
            $perPage = 20;
        }

        // Query builder untuk users
        $query = User::query();

        // Filter berdasarkan nama user
        if (request('search_nama')) {
            $query->where('nama_user', 'LIKE', '%' . request('search_nama') . '%');
        }

        // Filter berdasarkan username
        if (request('search_username')) {
            $query->where('username', 'LIKE', '%' . request('search_username') . '%');
        }

        // Filter berdasarkan email
        if (request('search_email')) {
            $query->where('email', 'LIKE', '%' . request('search_email') . '%');
        }

        // Filter berdasarkan role
        if (request('search_role')) {
            $query->where('role', request('search_role'));
        }

        // Filter berdasarkan status
        if (request('search_status')) {
            $query->where('status', request('search_status'));
        }

        // Ambil data dengan pagination
        $users = $query->orderByRaw('CAST(SUBSTRING(id_user, 4) AS UNSIGNED) ASC')
            ->paginate($perPage)
            ->appends(request()->except('page'));

        return view('module.setting-user.index', compact('users', 'allowed', 'perPage'));
    }
    // public function index()
    // {
    //     $allowed = [20, 40, 60, 80, 99];

    //     $perPage = (int) request('per_page', default: 20);
    //     if (!in_array($perPage, $allowed)) {
    //         $perPage = 20;
    //     }
    //     $users = User::orderByRaw('CAST(SUBSTRING(id_user, 4) AS UNSIGNED) ASC')
    //         ->paginate($perPage)
    //         ->appends(request()->except('page'));
    //     return view('module.setting-user.index', compact('users', 'allowed', 'perPage'));
    //     //$users = User::orderBy('created_at', 'desc')->get();
    //     // return view('module.setting-user.index', compact('users'));
    // }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        return view('module.setting-user.form', [
            'user' => new User(),
            'isEdit' => false
        ]);
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_user' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'nullable|email|max:200|unique:users,email',
            'password' => 'required|string|min:4|confirmed',
            'role' => 'required|in:admin,karyawanproduksi,owner',
            'no_telepon' => 'required|string|max:15|regex:/^[0-9]+$/',
            'status' => 'required|in:aktif,non_aktif',
        ], [
            'nama_user.required' => 'Nama user wajib diisi',
            'username.required' => 'Username wajib diisi',
            'username.unique' => 'Username sudah digunakan',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 4 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'role.required' => 'Role wajib dipilih',
            'no_telepon.required' => 'No telepon wajib diisi',
            'no_telepon.regex' => 'No telepon hanya boleh berisi angka',
            'status.required' => 'Status wajib dipilih',
        ]);

        // Generate ID User otomatis
        // $lastUser = User::orderBy('id_user', 'desc')->first();
        // if ($lastUser) {
        //     $lastNumber = (int) substr($lastUser->id_user, 1);
        //     $newNumber = $lastNumber + 1;
        // } else {
        //     $newNumber = 1;
        // }
        // $id_user = 'U' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
        
        // Generate ID User otomatis dengan format USR000000X
        $lastUser = User::orderBy('id_user', 'desc')->first();
        if ($lastUser && preg_match('/(\d+)$/', $lastUser->id_user, $matches)) {
            $lastNumber = (int) $matches[1];
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        $id_user = 'USR' . str_pad($newNumber, 2, '0', STR_PAD_LEFT);


        User::create([
            'id_user' => $id_user,
            'nama_user' => $validated['nama_user'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'no_telepon' => $validated['no_telepon'],
            'status' => $validated['status'],
        ]);

        return redirect()->route('setting-user.index')
            ->with('success', 'User berhasil ditambahkan');
    }

    /**
     * Show the form for editing user
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('module.setting-user.form', [
            'user' => $user,
            'isEdit' => true
        ]);
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'nama_user' => 'required|string|max:100',
            'username' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'username')->ignore($user->id_user, 'id_user')
            ],
            'email' => [
                'nullable',
                'email',
                'max:50',
                Rule::unique('users', 'email')->ignore($user->id_user, 'id_user')
            ],
            'password' => 'nullable|string|min:4|confirmed',
            'role' => 'required|in:admin,karyawanproduksi,owner',
            'no_telepon' => 'required|string|max:15|regex:/^[0-9]+$/',
            'status' => 'required|in:aktif,non_aktif',
        ], [
            'nama_user.required' => 'Nama user wajib diisi',
            'username.required' => 'Username wajib diisi',
            'username.unique' => 'Username sudah digunakan',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'password.min' => 'Password minimal 4 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'role.required' => 'Role wajib dipilih',
            'no_telepon.required' => 'No telepon wajib diisi',
            'no_telepon.regex' => 'No telepon hanya boleh berisi angka',
            'status.required' => 'Status wajib dipilih',
        ]);

        $dataUpdate = [
            'nama_user' => $validated['nama_user'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'no_telepon' => $validated['no_telepon'],
            'status' => $validated['status'],
        ];

        // Update password hanya jika diisi
        if (!empty($validated['password'])) {
            $dataUpdate['password'] = Hash::make($validated['password']);
        }

        $user->update($dataUpdate);

        return redirect()->route('setting-user.index')
            ->with('success', 'User berhasil diperbarui');
    }

    /**
     * Remove the specified user
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Cegah hapus diri sendiri
        if ($user->id_user === Auth::user()->id_user) {
            return redirect()->route('setting-user.index')
                ->with('error', 'Tidak dapat menghapus akun sendiri');
        }

        $user->delete();

        return redirect()->route('setting-user.index')
            ->with('success', 'User berhasil dihapus');
    }
}