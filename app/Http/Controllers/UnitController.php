<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class UnitController extends Controller
{
    public function login()
    {
        return view('login', ['title' => 'User | Login']);
    }

    private function upperWords(?string $text): string
    {
        return strtoupper(trim($text ?? ''));
    }

    function processLogin()
    {
        return Socialite::driver('google')->redirect();
    }

    function routeAdmin(Request $request)
    {
        $user = Socialite::driver('google')->stateless()->user(); //biarkan error, tpi jalan kok

        $allowed = [
            '@john.petra.ac.id',
            '@peter.petra.ac.id'
        ];

        $isAllowed = false;
        foreach ($allowed as $domain) {
            if (str_ends_with($user->email, $domain)) {
                $isAllowed = true;
                break;
            }
        }
        if (!$isAllowed) {
            return redirect()->route('user.login')->with('error', 'Mohon gunakan email Petra dengan @john.petra.ac.id atau @peter.petra.ac.id');
        }

        $mahasiswaUnit = Unit::where('name', 'MAHASISWA')->first();
        if (!$mahasiswaUnit) {
            return redirect()->route('user.login')->with('error', 'Unit Mahasiswa tidak ditemukan, silakan hubungi admin.');
        }

        $user = User::firstOrCreate(
            [
                'email' => $user->email,
            ],
            [
                'name' => $user->name,
                'email' => $user->email,
                'unit_id' => $mahasiswaUnit->id,
            ]
        );

        if (!$user && ! $user->unit_id) {
            return redirect()->route('user.login')->with('error', 'Gagal membuat atau mendapatkan user, silakan coba lagi.');
        }

        Auth::login($user);
        $request->session()->regenerate();

        if ($user->unit_id === $mahasiswaUnit->id) {
            return redirect()->route('user.landing');
        } else {
            return redirect()->route('admin.dashboard');
        }
    }


    function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->to('/login')->with('success', 'Logout Sukses');
    }

    public function formBooking()
    {
        // ambil labs (kayak yang di admin... 
        // liat petak" desks di setiap lab lalu cek apakah item lengkap | apa aja yang avail dipinjem)
        return view('user.booking');
    }

    public function storeBooking() {}
}
