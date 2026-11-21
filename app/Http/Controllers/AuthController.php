<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    private const MAX_LOGIN_ATTEMPTS = 3;

    private const LOGIN_DECAY_SECONDS = 60;

    public function showLogin(): Response
    {
        return response()
            ->view('auth.login')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $keys = $this->loginThrottleKeys($request, $validated['email']);
        $blockedKeys = array_filter(
            $keys,
            fn (string $key): bool => RateLimiter::tooManyAttempts($key, self::MAX_LOGIN_ATTEMPTS),
        );

        if ($blockedKeys !== []) {
            $blockedFor = max(array_map(
                fn (string $key): int => RateLimiter::availableIn($key),
                $blockedKeys,
            ));

            return back()
                ->withErrors(['email' => "Too many login attempts. Try again in {$blockedFor} seconds."])
                ->onlyInput('email');
        }

        if (Auth::attempt($validated)) {
            foreach ($keys as $key) {
                RateLimiter::clear($key);
            }

            $request->session()->regenerate();

            return redirect()->intended(route('users.index'));
        }

        foreach ($keys as $key) {
            RateLimiter::hit($key, self::LOGIN_DECAY_SECONDS);
        }

        $blockedKeys = array_filter(
            $keys,
            fn (string $key): bool => RateLimiter::tooManyAttempts($key, self::MAX_LOGIN_ATTEMPTS),
        );
        $blockedFor = $blockedKeys === [] ? 0 : max(array_map(
            fn (string $key): int => RateLimiter::availableIn($key),
            $blockedKeys,
        ));

        $message = $blockedFor > 0
            ? "Too many login attempts. Try again in {$blockedFor} seconds."
            : 'These credentials do not match our records.';

        return back()->withErrors(['email' => $message])->onlyInput('email');
    }

    public function showRegister(): Response
    {
        return response()
            ->view('auth.register')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(7)->letters()->mixedCase()->numbers()->symbols()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('users.index');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Limit both a particular account and the originating address.
     *
     * @return array<int, string>
     */
    private function loginThrottleKeys(Request $request, string $email): array
    {
        $normalizedEmail = Str::lower($email);
        $ipAddress = $request->ip() ?? 'unknown';

        return [
            'login:email:'.hash('sha256', $normalizedEmail),
            'login:ip:'.hash('sha256', $ipAddress),
        ];
    }
}
