<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginType = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $attemptData = [
            $loginType => $credentials['login'],
            'password' => $credentials['password'],
        ];

        if (Auth::attempt($attemptData, $request->filled('remember'))) {
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors([
                    'login' => __('Akaunti yako imezimwa. Wasiliana na Uongozi wa Famasi.'),
                ]);
            }

            $user->update(['last_login_at' => now()]);

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'login',
                'description' => "User {$user->name} ({$user->role}) logged in successfully.",
                'entity_type' => 'User',
                'entity_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);

            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Karibu ' . $user->name . '! Umefanikiwa kuingia kwenye mfumo wa NJAMA PHARMACY.');
        }

        return back()->withErrors([
            'login' => __('Barua pepe au nenosiri si sahihi. Jaribu tena.'),
        ])->onlyInput('login');
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'logout',
                'description' => "User " . Auth::user()->name . " logged out.",
                'entity_type' => 'User',
                'entity_id' => Auth::id(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'Umeondoka kwenye mfumo salama.');
    }

    public function profile()
    {
        return view('auth.profile', ['user' => Auth::user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:6|confirmed',
        ]);

        if ($request->filled('current_password')) {
            if (!\Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Nenosiri la sasa si sahihi.']);
            }
            $user->password = \Hash::make($request->new_password);
        }

        $user->name = $validated['name'];
        $user->phone = $validated['phone'];
        $user->save();

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'profile_update',
            'description' => "Profile updated for {$user->name}.",
            'entity_type' => 'User',
            'entity_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return back()->with('success', 'Taarifa zako zimesasishwa kikamilifu.');
    }
}
