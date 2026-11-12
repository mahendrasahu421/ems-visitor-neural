<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('auth.login');
    }

    /**
     * Handle Login form and authentication.
     */
    public function login(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        // If GET request without submission credentials, show the login view
        if ($request->isMethod('get') && !$request->filled('email')) {
            return view('auth.login');
        }

        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'Please enter your registered email address.',
            'password.required' => 'Please enter your password.',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember_me');

        if (!Auth::attempt($credentials, $remember)) {
            return back()->withInput($request->only('email'))->with('error', 'Invalid email or password. Please check your credentials.');
        }

        $user = Auth::user();

        // Check if user is active
        if ((int) $user->status !== 1) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Your account is currently inactive. Please contact the administrator.');
        }

        $request->session()->regenerate();

        // If employee role specifically mapped to separate portal
        $isEmployee = (int) $user->role_id === 4
            || (method_exists($user, 'hasRole') && $user->hasRole('Employee'));

        if ($isEmployee) {
            return redirect('/employee/dashboard')->with('success', 'Welcome, ' . $user->name . '!');
        }

        // All administrative and operational staff (Admin, Super Admin, HR, Supervisor, etc.)
        return redirect()->intended(route('home'))->with('success', 'Login successful. Welcome back, ' . $user->name . '!');
    }




    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function logout(Request $request)
    {

        Auth::logout();
        $request->session()->invalidate(); // Session destroy
        $request->session()->regenerateToken(); // CSRF token regenerate

        return redirect('/')->with('success', 'Logged out successfully.');
    }
}
