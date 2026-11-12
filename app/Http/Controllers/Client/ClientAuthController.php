<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientAuthController extends Controller
{
    public function index()
    {
        if (Auth::guard('client')->check()) {
            return redirect()->route('client.dashboard');
        }

        return view('client.auth.login', [
            'title' => 'Client Login',
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $credentials['status'] = 'Active';

        if (!Auth::guard('client')->attempt($credentials, $request->boolean('remember_me'))) {
            return back()
                ->withInput($request->only('email'))
                ->with('error', 'Invalid client credentials or inactive account.');
        }

        $request->session()->regenerate();

        return redirect()->route('client.dashboard')->with('success', 'Welcome to the client portal.');
    }

    public function logout(Request $request)
    {
        Auth::guard('client')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('client.login')->with('success', 'Logged out successfully.');
    }
}
