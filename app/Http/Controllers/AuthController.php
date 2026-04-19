<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:8',
            'password' => 'required|string|min:4',
        ], [
            'username.required' => 'Username wajib diisi',
            'username.max'      => 'Username maksimal 8 karakter',
            'password.required' => 'Password wajib diisi',
            'password.min'      => 'Password minimal 4 karakter',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)
                         ->withInput($request->only('username'));
        }

        $user = User::where('username', $request->username)
                    ->where('status', 'aktif')
                    ->first();

        if (!$user) {
            return back()->withErrors(['username' => 'Username tidak ditemukan atau akun tidak aktif'])
                         ->withInput($request->only('username'));
        }
        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            $request->session()->regenerate();
            
            // Return success response untuk AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Login berhasil!',
                    'role' => Auth::user()->role,
                    'redirect' => $this->getRedirectUrl(Auth::user()->role)
                ]);
            }
            
            return $this->redirectBasedOnRole(Auth::user()->role);
        }
        // $credentials = [
        //     'username' => $request->username,
        //     'password' => $request->password,
        //     'status'   => 'aktif',
        // ];

        // // Remember me untuk session
        // $remember = $request->boolean('remember');

        // if (Auth::attempt($credentials, $remember)) {
        //     $request->session()->regenerate(); 
            // Return success response untuk AJAX
        //     if ($request->ajax() || $request->wantsJson()) {
        //         return response()->json([
        //             'success' => true,
        //             'message' => 'Login berhasil!',
        //             'role' => Auth::user()->role,
        //             'redirect' => $this->getRedirectUrl(Auth::user()->role)
        //         ]);
        //     }
            
        //     return $this->redirectBasedOnRole(Auth::user()->role);
        // }

        // Return error response untuk AJAX
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Password salah'
            ], 401);
        }

        return back()->withErrors(['password' => 'Password salah'])
                     ->withInput($request->only('username'));
    }

    private function getRedirectUrl($role)
    {
        if ($role === 'admin') {
            return route('admin.dashboard');
        } elseif ($role === 'karyawanproduksi') {
            return route('produksi.dashboard');
        } elseif ($role === 'owner') {
            return route('owner.dashboard');
        }
        return route('dashboard');
    }

    private function redirectBasedOnRole($role)
    {
        if ($role === 'admin') {
            return redirect()->route('admin.dashboard')->with('success', 'Selamat datang, Administrator!');
        } elseif ($role === 'karyawanproduksi') {
            return redirect()->route('produksi.dashboard')->with('success', 'Selamat datang, Karyawan Produksi!');
        } elseif ($role === 'owner') {
            return redirect()->route('owner.dashboard')->with('success', 'Selamat datang, Owner!');
        }
        return redirect()->route('dashboard')->with('success', 'Login berhasil!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Logout berhasil!');
    }
}
// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Validator;
// use App\Models\User;


// class AuthController extends Controller
// {
//     public function showLoginForm()
//     {
//         if (Auth::check()) {
//             return redirect()->route('dashboard');
//         }
//         return view('auth.login');
//     }

//     public function login(Request $request)
//     {
//         $validator = Validator::make($request->all(), [
//             'username' => 'required|string|max:8',
//             'password' => 'required|string|min:4',
//         ], [
//             'username.required' => 'Username wajib diisi',
//             'username.max'      => 'Username maksimal 8 karakter',
//             'password.required' => 'Password wajib diisi',
//             'password.min'      => 'Password minimal 4 karakter',
//         ]);

//         if ($validator->fails()) {
//             return back()->withErrors($validator)
//                          ->withInput($request->only('username'));
//         }

//         $user = User::where('username', $request->username)
//                     ->where('status', 'aktif')
//                     ->first();

//         if (!$user) {
//             return back()->withErrors(['username' => 'Username tidak ditemukan atau akun tidak aktif'])
//                          ->withInput($request->only('username'));
//         }

//         $credentials = [
//             'username' => $request->username,
//             'password' => $request->password,
//             'status'   => 'aktif',
//         ];

//         $remember = $request->boolean('remember');

//         if (Auth::attempt($credentials, $remember)) {
//             $request->session()->regenerate();
//             return $this->redirectBasedOnRole(Auth::user()->role);
//         }

//         return back()->withErrors(['password' => 'Password salah'])
//                      ->withInput($request->only('username'));
//     }

//     private function redirectBasedOnRole($role)
//     {
//         if ($role === 'admin') {
//             return redirect()->route('admin.dashboard')->with('success', 'Selamat datang, Administrator!');
//         } elseif ($role === 'karyawanproduksi') {
//             return redirect()->route('produksi.dashboard')->with('success', 'Selamat datang, Karyawan Produksi!');
//         } elseif ($role === 'owner') {
//             return redirect()->route('owner.dashboard')->with('success', 'Selamat datang, Owner!');
//         }
//         return redirect()->route('dashboard')->with('success', 'Login berhasil!');
//     }

//     public function logout(Request $request)
//     {
//         Auth::logout();
//         $request->session()->invalidate();
//         $request->session()->regenerateToken();
//         return redirect()->route('login')->with('success', 'Logout berhasil!');
//     }
// }
// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Validator;
// use App\Models\User;

// class AuthController extends Controller
// {
//     public function showLoginForm()
//     {
//         if (Auth::check()) {
//             return redirect()->route('dashboard');
//         }
//         return view('auth.login'); // ini akan mencari resources/views/auth/login.blade.php
//     }

//     public function login(Request $request)
//     {
//         $validator = Validator::make($request->all(), [
//             'username' => 'required|string',
//             'password' => 'required|string',
//         ], [
//             'username.required' => 'Username wajib diisi',
//             'password.required' => 'Password wajib diisi',
//         ]);

//         if ($validator->fails()) {
//             return back()->withErrors($validator)
//                          ->withInput($request->only('username'));
//         }

//         // pastikan kolom 'username' dan 'status' ada di tabel users
//         $user = User::where('username', $request->username)
//                     ->where('status', 'aktif')
//                     ->first();

//         if (!$user) {
//             return back()->withErrors(['username' => 'Username tidak ditemukan atau akun tidak aktif'])
//                          ->withInput($request->only('username'));
//         }

//         $credentials = [
//             'username' => $request->username,
//             'password' => $request->password,
//             'status'   => 'aktif',
//         ];

//         $remember = $request->boolean('remember');

//         if (Auth::attempt($credentials, $remember)) {
//             $request->session()->regenerate();
//             return $this->redirectBasedOnRole(Auth::user()->role);
//         }

//         return back()->withErrors(['password' => 'Password salah'])
//                      ->withInput($request->only('username'));
//     }

//     private function redirectBasedOnRole($role)
//     {
//         return match ($role) {
//             'admin'            => redirect()->route('admin.dashboard')->with('success', 'Selamat datang, Administrator!'),
//             'karyawanproduksi' => redirect()->route('produksi.dashboard')->with('success', 'Selamat datang, Karyawan Produksi!'),
//             'owner'            => redirect()->route('owner.dashboard')->with('success', 'Selamat datang, Owner!'),
//             default            => redirect()->route('dashboard')->with('success', 'Login berhasil!'),
//         };
//     }

//     public function logout(Request $request)
//     {
//         Auth::logout();
//         $request->session()->invalidate();
//         $request->session()->regenerateToken();
//         return redirect()->route('login')->with('success', 'Logout berhasil!');
//     }
// }

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Validator;
// use App\Models\User;

// class AuthController extends Controller
// {
//     // Menampilkan halaman login
//     public function showLoginForm()
//     {
//         // Jika sudah login, redirect ke dashboard
//         if (Auth::check()) {
//             return redirect()->route('dashboard');
//         }
        
//         return view('auth.login');
//     }

//     // Proses login
//     public function login(Request $request)
//     {
//         // Validasi input
//         $validator = Validator::make($request->all(), [
//             'username' => 'required|string',
//             'password' => 'required|string',
//         ], [
//             'username.required' => 'Username wajib diisi',
//             'password.required' => 'Password wajib diisi',
//         ]);

//         if ($validator->fails()) {
//             return redirect()->back()
//                 ->withErrors($validator)
//                 ->withInput($request->only('username'));
//         }

//         // Cek apakah user ada dan statusnya aktif
//         $user = User::where('username', $request->username)
//                     ->where('status', 'aktif')
//                     ->first();

//         if (!$user) {
//             return redirect()->back()
//                 ->withErrors(['username' => 'Username tidak ditemukan atau akun tidak aktif'])
//                 ->withInput($request->only('username'));
//         }

//         // Attempt login
//         $credentials = [
//             'username' => $request->username,
//             'password' => $request->password,
//             'status' => 'aktif'
//         ];

//         $remember = $request->has('remember');

//         if (Auth::attempt($credentials, $remember)) {
//             $request->session()->regenerate();

//             // Redirect berdasarkan role
//             return $this->redirectBasedOnRole(Auth::user()->role);
//         }

//         return redirect()->back()
//             ->withErrors(['password' => 'Password salah'])
//             ->withInput($request->only('username'));
//     }

//     // Redirect berdasarkan role
//     private function redirectBasedOnRole($role)
//     {
//         switch ($role) {
//             case 'admin':
//                 return redirect()->route('admin.dashboard')
//                     ->with('success', 'Selamat datang, Administrator!');
//             case 'karyawanproduksi':
//                 return redirect()->route('produksi.dashboard')
//                     ->with('success', 'Selamat datang, Karyawan Produksi!');
//             case 'owner':
//                 return redirect()->route('owner.dashboard')
//                     ->with('success', 'Selamat datang, Owner!');
//             default:
//                 return redirect()->route('dashboard')
//                     ->with('success', 'Login berhasil!');
//         }
//     }

//     // Logout
//     public function logout(Request $request)
//     {
//         Auth::logout();
        
//         $request->session()->invalidate();
//         $request->session()->regenerateToken();
        
//         return redirect()->route('login')
//             ->with('success', 'Logout berhasil!');
//     }
// }