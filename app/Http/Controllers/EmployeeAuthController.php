<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use \Illuminate\Validation\ValidationException;

class EmployeeAuthController extends Controller
{
    public function index()
    {
        return view('employee.auth.login');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'login' => 'required',
                'password' => 'required',
            ]);

            $loginInput = $request->login;
            $password   = $request->password;
            $remember   = $request->has('remember_me');

            $credentialsList = [
                ['employee_name'  => $loginInput, 'password' => $password],
                ['employee_email' => $loginInput, 'password' => $password],
                ['employee_mobile' => $loginInput, 'password' => $password],
            ];
            foreach ($credentialsList as $credentials) {

                if (Auth::guard('employee')->attempt($credentials, $remember)) {

                    $user = Auth::guard('employee')->user();

                    if (!$user->isActive()) {
                        Auth::guard('employee')->logout();
                        $request->session()->invalidate();
                        $request->session()->regenerateToken();

                        return back()->with('error', 'Your employee account is currently inactive. Please contact HR or Administrator.')->withInput();
                    }

                    return redirect()
                        ->route('employee.dashboard')
                        ->with('success', 'Welcome back, ' . $user->employee_name . '!');
                }
            }

            return back()->with('error', 'Invalid credentials')->withInput();
        } catch (ValidationException $e) {
            $message = collect($e->errors())->flatten()->first();

            return back()
                ->with('error', $message ?? 'Please fill all required fields correctly.')
                ->withInput();
        } catch (\Exception $e) { print_r($e);
            return back()->with('error', 'Something went wrong, please try again.')->withInput();
        }
    }

    public function logout(Request $request)
    {
        Auth::guard('employee')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/employee/login')->with('success', 'Logged out successfully.');
    }
}
