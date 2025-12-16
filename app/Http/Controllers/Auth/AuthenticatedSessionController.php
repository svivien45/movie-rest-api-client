<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

public function store(LoginRequest $request): RedirectResponse
{

    $response = Http::api()->post('/users/login', [
        'email' => $request->email,
        'password' => $request->password,
    ]);

    if ($response->successful()) {
        $responseBody = json_decode($response->body());

        if (empty($responseBody->user)) {
            return back()->withErrors([
                'message' => $responseBody->message ?? 'Hibás bejelentkezési adatok',
            ]);
        }

        session([
            'api_token'  => $responseBody->user->token,
            'user_name'  => $responseBody->user->email, 
            'user_email' => $responseBody->user->email,
        ]);

        return redirect()->intended('/movies'); 
    }

    // Ha nem sikeres a válasz
    return back()->withErrors([
        'email' => 'Hibás bejelentkezési adatok',
    ]);
}

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        session()->forget('api_token');
        session()->forget('user_name');
        session()->forget('user_email');

        return redirect('/');
    }
}
