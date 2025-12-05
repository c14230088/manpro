<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $loggedUser = Auth::user();
        if (!$loggedUser) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mohon login terlebih dahulu.'
                ], 401);
            }
            return redirect()->route('user.login')->with('error', 'Mohon login terlebih dahulu.');
        }
        $user = User::find($loggedUser->id)->load('unit.permissions', 'permissions');

        $currentRouteName = $request->route()->getName();

        // Jika route tidak punya nama (misal closure route), skip atau block
        if (!$currentRouteName) {
            // INI MASIH ALLOW KERJA, Atau lebih aman di-block saja (?)
            return $next($request);
        }

        $requiredPermission = Permission::where('route', $currentRouteName)->first();
        if ($requiredPermission) {

            // Cek Permission Milik User
            $hasDirectPermission = $user->permissions->contains('id', $requiredPermission->id);

            // Cek Permission Milik Unit si user
            $hasUnitPermission = false;
            if ($user->unit) {
                $hasUnitPermission = $user->unit->permissions->contains('id', $requiredPermission->id);
            }

            // Jika User TIDAK punya dan Unit juga TIDAK punya -> Forbidden
            if (!$hasDirectPermission && !$hasUnitPermission) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak memiliki izin untuk mengakses halaman ini.'
                    ], 403);
                }
                Log::info($user?->permissions);
                Log::info($user?->unit?->permissions);
                Log::info('Unauthorized access attempt by user ID: ' . $user?->id . ' to route: ' . $currentRouteName . '. Required permission ID: ' . $requiredPermission->id . ' permission name: ' . $requiredPermission->name);
                abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
                // return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk mengakses halaman ini.');
            }
        }

        // Jika route ada dalam middleware ini tapi 
        // tidak terdaftar di tabel permissions, route itu "Open" untuk semua user yang login.
        return $next($request);
    }
}
