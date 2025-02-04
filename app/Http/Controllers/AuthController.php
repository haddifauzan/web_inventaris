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
            return redirect()->route('dashboard.index');
        }
        return view('login');
    }

    /**
     * Handle user login.
     */
    public function login(Request $request)
    {
        // Validasi input login
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $remember = $request->input('remember') === 'on';

        if ($this->hasTooManyLoginAttempts($request)) {
            $seconds = $this->rateLimiter->availableIn($this->throttleKey($request));
            return back()->with('throttle', $seconds );
        }

        // Coba login
        if (Auth::attempt($request->only('username', 'password'), $remember)) {
            $request->session()->regenerate();
            if ($remember) {
                $cookie = $this->setRememberMeCookie(Auth::user());
                cookie()->queue($cookie);
            }
            return redirect()->route('dashboard.index')->with('success', 'Login berhasil');
        }

        // Jika login gagal, tingkatkan percobaan login
        $this->incrementLoginAttempts($request);
        $attemptsLeft = max(0, $this->maxAttempts() - $this->rateLimiter->attempts($this->throttleKey($request)));
        return back()->with('error', 'Username atau Password salah! Percobaan login tersisa ' . $attemptsLeft . ' kali.');
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Logout berhasil');
    }

    /**
     * Update username and password.
     */
    public function updateCredentials(Request $request, $id)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::findOrFail($id);
        $user->username = $request->username;
        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Kredensial berhasil diperbarui');
    }

    /**
     * Reset username and password to default using kode_reset.
     */
    public function resetCredential(Request $request)
    {
        $request->validate([
            'kode_reset' => 'required|string',
        ]);

        $user = User::where('kode_reset', $request->kode_reset)->first();

        if (!$user) {
            return back()->with('error', 'Kode reset tidak valid');
        }

        $user->username = 'admin';
        $user->password = Hash::make('admin123');
        $user->save();

        return back()->with('success', 'Kredensial berhasil direset ke default. \n username: admin, password: admin123');
    }

    // Check if the user has too many login attempts
    protected function hasTooManyLoginAttempts(Request $request)
    {
        return $this->rateLimiter->tooManyAttempts($this->throttleKey($request), $this->maxAttempts());
    }

    // Get the maximum number of login attempts
    protected function maxAttempts()
    {
        return 5; 
    }

    // Get the number of minutes to throttle the login attempts
    protected function decayMinutes()
    {
        return 1; // Set the throttle time to 1 minute
    }

    // Create a unique key for the throttle
    protected function throttleKey(Request $request)
    {
        return Str::lower($request->input('username')) . '|' . $request->ip();
    }

    // Increment the number of login attempts
    protected function incrementLoginAttempts(Request $request)
    {
        $this->rateLimiter->hit($this->throttleKey($request), $this->decayMinutes() * 60); // Store attempts for 1 minute
    }

    // Set a custom "remember me" cookie
    protected function setRememberMeCookie($user)
    {
        $token = Str::random(60);
        $user->remember_token = $token;
        $user->save();
        return cookie('remember_me', $token, 60 * 60 * 24 * 30); // Cookie valid for 30 days
    }
}
