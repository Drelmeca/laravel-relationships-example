<?php

use App\Http\Controllers\AuthController;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1')->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

$usersPage = function () {
    $users = User::with(['profile', 'posts', 'roles'])
        ->orderBy('id')
        ->get();

    return response()
        ->view('users.index', compact('users'))
        ->header('Cache-Control', 'private, no-store, no-cache, must-revalidate');
};

Route::middleware('auth')->group(function () use ($usersPage) {
    Route::get('/', $usersPage)->name('home');
    Route::get('/users', $usersPage)->name('users.index');

    Route::post('/users', function (Request $request): RedirectResponse {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
        ]);

        User::create($validated);

        return redirect()
            ->route('users.index')
            ->with('status', 'User created successfully.');
    })->middleware('throttle:10,1')->name('users.store');
});
