<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        \Log::info('Registration started');
        
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'terms' => ['required', 'accepted'],
        ]);

        \Log::info('Creating user', ['email' => $request->email]);
        
        // Create user (VERIFIED STATUS WILL BE NULL)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 'ACTIVE',
            'gender' => null,
            'email_verified_at' => null, // Explicitly set to null
        ]);

        // Assign USER role
        $userRole = Role::where('name', 'USER')->first();
        if ($userRole) {
            $user->roles()->attach($userRole->id);
        }

        \Log::info('User created', ['user_id' => $user->id]);
        
        // Send verification code
        $code = $user->sendVerificationCode();
        \Log::info('Verification code sent', ['code' => $code]);

        // IMPORTANT: Don't login here - just redirect to verification
        // Store user email in session to identify them
        $request->session()->put('verifying_user_email', $user->email);
        
        \Log::info('Redirecting to verification', [
            'session_email' => session('verifying_user_email')
        ]);

        // Redirect to verification page
        return redirect()->route('verification.show')
            ->with('success', 'Registration successful! Check your email for verification code.');
    }
}