<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        $this->refreshCaptcha();

        return view('auth.login');
    }
    
    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        // Rate limiting: 5 attempts per minute per IP+email combo
        $throttleKey = Str::transliterate(Str::lower($request->input('email', '')) . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->refreshCaptcha();

            return back()->withErrors([
                'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ])->withInput($request->except('password', 'captcha'));
        }

        // Validate input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
            'captcha' => 'required|numeric',
        ], [
            'email.required' => 'Email address is required',
            'email.email' => 'Please enter a valid email address',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 6 characters',
            'captcha.required' => 'Please solve the math problem',
            'captcha.numeric' => 'Captcha answer must be a number',
        ]);
        
        // Verify captcha
        $correctAnswer = session('captcha_answer');
        if ((int)$request->captcha !== (int)$correctAnswer) {
            // Generate new captcha
            $this->refreshCaptcha();
            
            return back()->withErrors([
                'captcha' => 'Incorrect answer. Please try again.'
            ])->withInput($request->except('password', 'captcha'));
        }
        
        // Attempt to log the user in
        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');
        
        if (Auth::attempt($credentials, $remember)) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            // Check if user is active
            if (!Auth::user()->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Your account has been deactivated. Please contact the administrator.'
                ])->withInput($request->except('password', 'captcha'));
            }

            // Log the activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'login',
                'description' => 'User logged in successfully',
                'ip_address' => $request->ip(),
                'created_at' => now(),
            ]);

            return redirect()->intended(route('admin.dashboard'));
        }

        // Count failed attempt
        RateLimiter::hit($throttleKey, 60);

        // Generate new captcha on failed login
        $this->refreshCaptcha();

        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ])->withInput($request->except('password', 'captcha'));
    }
    
    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        // Log the activity
        if (Auth::check()) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'logout',
                'description' => 'User logged out',
                'ip_address' => $request->ip(),
                'created_at' => now(),
            ]);
        }
        
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('home')->with('success', 'You have been logged out successfully.');
    }
    
    /**
     * Refresh captcha via AJAX
     */
    public function refreshCaptchaAjax(Request $request)
    {
        $num1 = rand(1, 20);
        $num2 = rand(1, 20);

        // Use random operations for stronger captcha
        $operators = ['+', '-', '×'];
        $operator = $operators[array_rand($operators)];

        switch ($operator) {
            case '+':
                $answer = $num1 + $num2;
                break;
            case '-':
                // Ensure positive result
                if ($num2 > $num1) { [$num1, $num2] = [$num2, $num1]; }
                $answer = $num1 - $num2;
                break;
            case '×':
                $num1 = rand(2, 12);
                $num2 = rand(2, 9);
                $answer = $num1 * $num2;
                break;
        }

        session([
            'captcha_num1' => $num1,
            'captcha_num2' => $num2,
            'captcha_operator' => $operator,
            'captcha_answer' => $answer,
        ]);

        return response()->json([
            'success' => true,
            'num1' => $num1,
            'num2' => $num2,
            'operator' => $operator,
        ]);
    }
    
    /**
     * Refresh captcha (internal method)
     */
    private function refreshCaptcha()
    {
        $num1 = rand(1, 20);
        $num2 = rand(1, 20);

        $operators = ['+', '-', '×'];
        $operator = $operators[array_rand($operators)];

        switch ($operator) {
            case '+':
                $answer = $num1 + $num2;
                break;
            case '-':
                if ($num2 > $num1) { [$num1, $num2] = [$num2, $num1]; }
                $answer = $num1 - $num2;
                break;
            case '×':
                $num1 = rand(2, 12);
                $num2 = rand(2, 9);
                $answer = $num1 * $num2;
                break;
        }

        session([
            'captcha_num1' => $num1,
            'captcha_num2' => $num2,
            'captcha_operator' => $operator,
            'captcha_answer' => $answer,
        ]);
    }
}
