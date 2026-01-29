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
    \Log::info('Registration started', [
        'session_id' => session()->getId(),
        'csrf_token' => csrf_token()
    ]);
    
    $request->validate([
        'name' => ['required', 'string', 'max:100'],
        'email' => ['required', 'string', 'email', 'max:150', 'unique:users'],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
        'terms' => ['required', 'accepted'],
    ]);

    \Log::info('Creating user', ['email' => $request->email]);
    
    // Create user
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'status' => 'ACTIVE',
        'gender' => null,
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

    // IMPORTANT: Manually login and regenerate session
    Auth::login($user);
    $request->session()->regenerate(); // This is critical!
    
    \Log::info('User logged in after registration', [
        'session_id' => session()->getId(),
        'user_id' => Auth::id()
    ]);

    // Redirect to verification page
    return redirect()->route('verification.show')
        ->with('success', 'Registration successful! Check your email for verification code.');
}
}