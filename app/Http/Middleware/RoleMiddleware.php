<?php 
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect()->route('login')
                ->withErrors(['error' => 'Silakan login terlebih dahulu']);
        }

        // Cek role user
        if (Auth::user()->role !== $role) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }

        return $next($request);
    }
}
// class RoleMiddleware
// {
//     /**
//      * Handle an incoming request.
//      *
//      * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
//      */
//     public function handle(Request $request, Closure $next, string $role): Response
//     {
//         // Cek apakah user sudah login
//         if (!auth()->check()) {
//             return redirect()->route('login')
//                 ->withErrors(['error' => 'Silakan login terlebih dahulu']);
//         }

//         // Cek role user
//         if (auth()->user()->role !== $role) {
//             abort(403, 'Anda tidak memiliki akses ke halaman ini');
//         }

//         return $next($request);
//     }
// }