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
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'terms' => ['required', 'accepted'],
        ]);

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

        // Send verification email
        $code = $user->sendVerificationCode();

        // Log the user in
        Auth::login($user);

        // Go directly to verification page
        return redirect()->route('verification.show');
    }
}