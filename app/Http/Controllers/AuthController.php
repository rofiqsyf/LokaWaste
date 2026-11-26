<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\User;
use App\Services\EmsifaWilayahService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            if (!Auth::user()->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Akun Anda saat ini dinonaktifkan. Hubungi administrator untuk informasi lebih lanjut.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();
            return redirect()->intended(route('home'))->with('success', 'Selamat datang kembali, ' . Auth::user()->name . '!');
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi tidak cocok dengan data kami.',
        ])->onlyInput('email');
    }

    public function showRegister(EmsifaWilayahService $wilayahService)
    {
        $provinces = $wilayahService->getProvinces();
        $districts = District::with('regency')->orderBy('name')->get();
        return view('auth.register', compact('provinces', 'districts'));
    }

    public function register(Request $request, EmsifaWilayahService $wilayahService)
    {
        // Enforce mass-assignment protection: role CANNOT be passed from form (TC-26, SDD 8.2)
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
            'district_id' => ['nullable', 'string', 'max:50'],
            'district_code' => ['nullable', 'string', 'max:50'],
            'district_name' => ['nullable', 'string', 'max:255'],
            'regency_id' => ['nullable', 'string', 'max:50'],
            'regency_name' => ['nullable', 'string', 'max:255'],
            'province_id' => ['nullable', 'string', 'max:50'],
            'province_name' => ['nullable', 'string', 'max:255'],
            'organization_name' => ['nullable', 'string', 'max:255'],
        ]);

        $districtId = null;
        $targetDistrict = $validated['district_code'] ?? $validated['district_id'] ?? null;
        if ($targetDistrict) {
            $district = $wilayahService->syncOrCreateDistrict(
                $targetDistrict,
                $request->input('district_name'),
                $request->input('regency_id'),
                $request->input('regency_name'),
                $request->input('province_id'),
                $request->input('province_name')
            );
            $districtId = $district?->id;
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'district_id' => $districtId,
            'organization_name' => $validated['organization_name'] ?? null,
            'email_verified_at' => now(), // Auto-verified for seamless demo
        ]);

        Auth::login($user);

        return redirect()->route('home')->with('success', 'Pendaftaran berhasil! Selamat datang di LokaWaste.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('info', 'Anda telah keluar.');
    }
}
