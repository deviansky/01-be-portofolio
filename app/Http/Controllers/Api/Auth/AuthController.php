<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Login admin panel.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            'remember' => ['boolean'],
        ]);

        $username = (string) $request->input('username');
        $throttleKey = Str::transliterate(Str::lower($username) . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return response()->json([
                'message' => 'Too Many Requests.',
            ], 429);
        }

        $credentials = [
            'username' => $username,
            'password' => $request->input('password'),
        ];
        $remember = (bool) $request->input('remember', false);

        if (! Auth::guard('web')->attempt($credentials, $remember)) {
            RateLimiter::hit($throttleKey, 60);

            return response()->json([
                'message' => 'Username atau password salah.',
                'errors' => [
                    'username' => ['Username atau password salah.'],
                ],
            ], 422);
        }

        RateLimiter::clear($throttleKey);
        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        $user = Auth::guard('web')->user();

        return response()->json([
            'id' => $user->id,
            'username' => $user->username,
            'name' => $user->name,
        ], 200);
    }

    /**
     * Logout admin panel.
     */
    public function logout(Request $request): Response
    {
        Auth::guard('web')->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return response()->noContent();
    }

    /**
     * Ambil data user yang sedang login.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'id' => $user->id,
            'username' => $user->username,
            'name' => $user->name,
        ], 200);
    }
}
