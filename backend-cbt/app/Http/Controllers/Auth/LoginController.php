<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\ActivityLog;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('login', 'password');

        $loginValue = $credentials['login'];
        $loginField = filter_var($loginValue, FILTER_VALIDATE_EMAIL) ? 'email' : 'nisn';

        $credentials = [$loginField => $loginValue, 'password' => $credentials['password']];

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            if ($loginField === 'nisn') {
                $credentials = ['nip' => $loginValue, 'password' => $credentials['password']];
                Auth::attempt($credentials, $request->boolean('remember'));
            }
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'login' => 'Kredensial tidak valid.',
            ])->onlyInput('login');
        }

        $request->session()->regenerate();

        $user = Auth::user();
        if (! $user->is_active) {
            Auth::logout();

            return back()->withErrors([
                'login' => 'Akun Anda tidak aktif. Silakan hubungi administrator.',
            ]);
        }

        ActivityLogger::log(ActivityLog::ACTION_LOGIN, 'Pengguna login ke sistem.', user: $user);

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            ActivityLogger::log(ActivityLog::ACTION_LOGOUT, 'Pengguna logout dari sistem.', user: $user);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
