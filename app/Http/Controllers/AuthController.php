<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Cache\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    protected $rateLimiter;

    public function __construct(RateLimiter $rateLimiter)
    {
        $this->rateLimiter = $rateLimiter;
    }

    public function showLoginForm()
    {
        if (Auth::check()) {
            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('user.dashboard');
        }
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $remember = $request->input('remember') === 'on';

        if ($this->hasTooManyLoginAttempts($request)) {
            $seconds = $this->rateLimiter->availableIn($this->throttleKey($request));
            return back()->with('throttle', $seconds);
        }

        if (Auth::attempt($request->only('username', 'password'), $remember)) {
            $request->session()->regenerate();
            
            if ($remember) {
                $cookie = $this->setRememberMeCookie(Auth::user());
                cookie()->queue($cookie);
            }

            // Redirect based on role
            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard')->with('success', 'Login berhasil');
            }
            return redirect()->route('user.dashboard')->with('success', 'Login berhasil');
        }

        $this->incrementLoginAttempts($request);
        $attemptsLeft = max(0, $this->maxAttempts() - $this->rateLimiter->attempts($this->throttleKey($request)));
        return back()->with('error', 'Username atau Password salah! Percobaan login tersisa ' . $attemptsLeft . ' kali.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Logout berhasil');
    }

    public function updateCredentials(Request $request, $id)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::findOrFail($id);
        
        // User can only update their own credentials
        if (Auth::id() !== $user->id_user) {
            return back()->with('error', 'Unauthorized action');
        }

        $user->username = $request->username;
        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Kredensial berhasil diperbarui');
    }

    public function resetCredential(Request $request)
    {
        $request->validate([
            'kode_reset' => 'required|string',
        ]);

        $user = User::where('kode_reset', $request->kode_reset)->first();

        if (!$user) {
            return back()->with('error', 'Kode reset tidak valid');
        }

        if ($user->role === 'admin') {
            $user->username = 'admin';
            $user->password = Hash::make('admin123');
        } else {
            $user->username = 'user';
            $user->password = Hash::make('user123');
        }
        
        $user->save();

        $credentials = $user->role === 'admin' 
            ? 'username: admin, password: admin123' 
            : 'username: user, password: user123';

        return back()->with('success', 'Kredensial berhasil direset ke default. ' . $credentials);

        return back()->with('success', 'Kredensial berhasil direset ke default. \n username: admin, password: admin123');
    }

    // Helper methods remain the same...
    protected function hasTooManyLoginAttempts(Request $request)
    {
        return $this->rateLimiter->tooManyAttempts($this->throttleKey($request), $this->maxAttempts());
    }

    protected function maxAttempts()
    {
        return 5;
    }

    protected function decayMinutes()
    {
        return 1;
    }

    protected function throttleKey(Request $request)
    {
        return Str::lower($request->input('username')) . '|' . $request->ip();
    }

    protected function incrementLoginAttempts(Request $request)
    {
        $this->rateLimiter->hit($this->throttleKey($request), $this->decayMinutes() * 60);
    }

    protected function setRememberMeCookie($user)
    {
        $token = Str::random(60);
        $user->remember_token = $token;
        $user->save();
        return cookie('remember_me', $token, 60 * 60 * 24 * 30);
    }
}