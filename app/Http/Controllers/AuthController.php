<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Cache\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    protected $rateLimiter;
    const MAX_ATTEMPTS = 5;
    const DECAY_MINUTES = 1;
    const REMEMBER_TOKEN_LENGTH = 60;

    public function __construct(RateLimiter $rateLimiter)
    {
        $this->rateLimiter = $rateLimiter;
    }

    public function showLoginForm()
    {
        if (Auth::check()) {
            return Auth::user()->role === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('user.dashboard');
        }
        return view('login');
    }

    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'username' => 'required|string|max:255',
                'password' => 'required|string',
            ]);

            // Cek jika sudah terlalu banyak percobaan
            if ($this->hasTooManyLoginAttempts($request)) {
                $seconds = $this->rateLimiter->availableIn($this->throttleKey($request));
                // Memastikan tampilan waktu selalu 60 detik ketika baru diblokir
                $seconds = ($this->rateLimiter->attempts($this->throttleKey($request)) == self::MAX_ATTEMPTS) 
                    ? self::DECAY_MINUTES * 60 
                    : $seconds;
                    
                return back()
                    ->withInput($request->except('password'))
                    ->with('throttle', $seconds);
            }

            $remember = $request->boolean('remember');

            if (Auth::attempt($credentials, $remember)) {
                $request->session()->regenerate();
                
                if ($remember) {
                    $this->setRememberMeCookie(Auth::user());
                }

                // Clear rate limiter jika login berhasil
                $this->rateLimiter->clear($this->throttleKey($request));
                
                // Redirect berdasarkan peran user
                if (Auth::user()->role === 'admin') {
                    return redirect()->route('admin.dashboard')->with('success', 'Login berhasil');
                }
                return redirect()->route('user.dashboard')->with('success', 'Login berhasil');
            }

            // Increment attempts setelah login gagal
            $this->incrementLoginAttempts($request);
            
            // Hitung attempts yang tersisa
            $attemptsLeft = self::MAX_ATTEMPTS - $this->rateLimiter->attempts($this->throttleKey($request));
            $attemptsLeft = max(1, $attemptsLeft + 1);
            
            return back()
                ->withInput($request->except('password'))
                ->with('error', "Username atau Password salah! Percobaan login tersisa {$attemptsLeft} kali.");
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat login. Silakan coba lagi.');
        }
    }

    public function logout(Request $request)
    {
        try {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()
                ->route('login')
                ->with('success', 'Logout berhasil');
        } catch (\Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat logout.');
        }
    }

    public function updateCredentials(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'username' => 'required|string|max:255|unique:users,username,' . $id,
                'password' => ['required', 'confirmed', Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                ],
            ]);

            $user = User::findOrFail($id);
            
            if ($user->id !== Auth::id() && !Auth::user()->isAdmin()) {
                return back()->with('error', 'Unauthorized access');
            }

            $user->update([
                'username' => $validated['username'],
                'password' => Hash::make($validated['password']),
            ]);

            return back()->with('success', 'Kredensial berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Update credentials error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memperbarui kredensial.');
        }
    }

    public function resetCredential(Request $request)
    {
        try {
            $validated = $request->validate([
                'kode_reset' => 'required|string|exists:tbl_user,kode_reset',
            ]);

            $user = User::where('kode_reset', $validated['kode_reset'])->first();
            
            if ($user->role === 'admin') {
                $defaultUsername = 'admin';
                $defaultPassword = 'admin123';
            } else {
                $defaultUsername = 'user';
                $defaultPassword = 'user123';
            }

            $user->update([
                'username' => $defaultUsername,
                'password' => Hash::make($defaultPassword),
            ]);


            return back()->with('success', sprintf(
                'Kredensial berhasil direset ke default.\nUsername: %s ,\nPassword: %s',
                $defaultUsername,
                $defaultPassword
            ));
        } catch (\Exception $e) {
            Log::error('Reset credential error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mereset kredensial.');
        }
    }

    protected function hasTooManyLoginAttempts(Request $request)
    {
        return $this->rateLimiter->tooManyAttempts(
            $this->throttleKey($request), 
            self::MAX_ATTEMPTS
        );
    }

    protected function throttleKey(Request $request)
    {
        // Hanya menggunakan IP address untuk throttling
        return 'login|' . $request->ip();
    }

    protected function incrementLoginAttempts(Request $request)
    {
        // Reset decay time ke 60 detik setiap kali hit
        $this->rateLimiter->hit(
            $this->throttleKey($request), 
            self::DECAY_MINUTES * 60
        );
    }

    protected function setRememberMeCookie($user)
    {
        $token = Str::random(self::REMEMBER_TOKEN_LENGTH);
        
        $user->update(['remember_token' => $token]);
        
        cookie()->queue(
            'remember_me', 
            $token, 
            60 * 24 * 30 // 30 days
        );
    }
}