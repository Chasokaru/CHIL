<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class UserController extends Controller
{
    /**
     * Show the login form.
     *
     * @return View
     */
    public function showLoginForm(): View
    {
        // Log when the login form is accessed
        Log::info('Login form accessed by a user.');

        // Return the login view with additional hints or tips
        return view('login', [
            'instructions' => 'Enter your username and password to log in.',
        ]);
    }

    /**
     * Handle a login request to the application.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $this->validateLogin($request);

        if (Auth::attempt($credentials)) {
            Log::info('User authenticated successfully.', ['username' => $credentials['username']]);
            $request->session()->regenerate();
            return redirect()->intended('/')->with('success', 'Welcome back!');
        }

        $this->logLoginError($credentials['username']);
        return redirect()->back()
            ->withErrors(['username' => 'Invalid credentials.'])
            ->withInput(['username' => $credentials['username']]);
    }
    private function validateLogin(Request $request): array
    {
        return $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
    }
    private function logLoginError(string $username)
    {
        Log::warning('Failed login attempt.', ['username' => $username]);
    }



    /**
     * Log the user out of the application.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function logout(Request $request): RedirectResponse
    {
        // Log the logout action
        Log::info('User logged out.', ['username' => Auth::user()->username ?? 'Guest']);

        // Perform the logout operation
        Auth::logout();

        // Invalidate the session to prevent reuse
        $request->session()->invalidate();

        // Regenerate the CSRF token for security
        $request->session()->regenerateToken();

        // Redirect to the homepage with a goodbye message
        return redirect('/')->with('success', 'You have been logged out successfully.');
    }
}
