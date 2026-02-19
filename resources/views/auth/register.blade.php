<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'RMS') }} - Register</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Exact colors from your login UI */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Figtree', 'Inter', sans-serif;
            background: #f0f4f8;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 1.5rem;
        }
        .split-card {
            max-width: 1100px;
            width: 100%;
            background: white;
            border-radius: 2.5rem;
            box-shadow: 0 30px 60px -15px rgba(0, 20, 30, 0.3);
            display: grid;
            grid-template-columns: 1fr 1fr;
            overflow: hidden;
            transition: all 0.2s ease;
        }
        /* BRAND PANEL – same as login */
        .brand-panel {
            background: linear-gradient(155deg, #174455 0%, #286b7f 100%);
            padding: 2.8rem 2.5rem;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            isolation: isolate;
        }
        .brand-panel::after {
            content: '';
            position: absolute;
            inset: 0;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 60 60" opacity="0.1"><path fill="white" d="M12 8h8v8h-8zM28 8h8v8h-8zM44 8h8v8h-8zM12 24h8v8h-8zM28 24h8v8h-8zM44 24h8v8h-8zM12 40h8v8h-8zM28 40h8v8h-8zM44 40h8v8h-8z"/></svg>');
            background-size: 50px;
            opacity: 0.2;
            z-index: -1;
        }
        .brand-header {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            margin-bottom: 2rem;
        }
        .brand-header i {
            font-size: 2.5rem;
            color: #ffdb9f;
            filter: drop-shadow(2px 4px 4px rgba(0,0,0,0.2));
        }
        .brand-header h1 {
            font-weight: 700;
            font-size: 2.2rem;
            letter-spacing: -0.02em;
            color: white;
        }
        .brand-header .badge {
            background: rgba(255,255,240,0.2);
            backdrop-filter: blur(4px);
            padding: 0.25rem 0.9rem;
            border-radius: 40px;
            font-size: 0.8rem;
            font-weight: 500;
            margin-left: 0.75rem;
            border: 1px solid rgba(255,255,200,0.3);
        }
        .hero-text {
            margin: 2rem 0 2.8rem;
        }
        .hero-text p {
            font-size: 1.8rem;
            font-weight: 500;
            line-height: 1.2;
            opacity: 0.95;
            text-shadow: 0 2px 3px rgba(0,0,0,0.1);
        }
        .hero-text p:last-of-type {
            font-size: 1.1rem;
            font-weight: 400;
            margin-top: 1rem;
            opacity: 0.8;
        }
        .feature-grid {
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
            margin: 1.5rem 0 1rem;
        }
        .feature-item {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .feature-item i {
            width: 2rem;
            font-size: 1.4rem;
            color: #ffdb9f;
        }
        .feature-item span {
            font-size: 1rem;
            font-weight: 500;
        }
        /* RIGHT PANEL - REGISTER FORM (same styling as login) */
        .form-panel {
            background: white;
            padding: 2.8rem 2.5rem;
            display: flex;
            flex-direction: column;
        }
        .form-panel h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #1e2f3d;
            margin-bottom: 0.2rem;
        }
        .form-sub {
            color: #59758b;
            font-size: 0.95rem;
            margin-bottom: 2rem;
            border-left: 3px solid #286b7f;
            padding-left: 0.9rem;
        }
        .input-group {
            margin-bottom: 1.6rem;
        }
        .input-label {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.4rem;
            font-weight: 500;
            font-size: 0.9rem;
            color: #2c3f50;
        }
        .input-label label {
            font-weight: 600;
        }
        .input-field {
            position: relative;
            display: flex;
            align-items: center;
        }
        .input-field i.icon-left {
            position: absolute;
            left: 1rem;
            color: #8faec2;
            font-size: 1rem;
        }
        .input-field input {
            width: 100%;
            padding: 0.9rem 1rem 0.9rem 2.7rem;
            border: 1.5px solid #e2eaf0;
            border-radius: 1.2rem;
            font-size: 1rem;
            font-weight: 500;
            transition: 0.2s;
            background: #f9fcff;
        }
        .input-field input:focus {
            outline: none;
            border-color: #286b7f;
            background: white;
            box-shadow: 0 6px 12px -8px rgba(28, 85, 104, 0.3);
        }
        .toggle-pw {
            position: absolute;
            right: 1rem;
            background: none;
            border: none;
            color: #7d9ab3;
            font-size: 1.2rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.25rem;
            transition: color 0.2s;
        }
        .toggle-pw:hover {
            color: #174455;
        }
        .checkbox-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 1.8rem 0 1.5rem;
        }
        .checkbox-row input {
            width: 1.1rem;
            height: 1.1rem;
            accent-color: #286b7f;
        }
        .checkbox-row label {
            color: #345b70;
            font-weight: 500;
            font-size: 0.95rem;
        }
        .btn-signin {
            background: #174455;
            border: none;
            color: white;
            font-weight: 700;
            font-size: 1.1rem;
            padding: 1rem;
            border-radius: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            cursor: pointer;
            transition: 0.2s;
            box-shadow: 0 8px 14px -8px #0b2a36;
            width: 100%;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .btn-signin:hover {
            background: #1f556b;
            transform: translateY(-2px);
            box-shadow: 0 15px 20px -10px #0f3340;
        }
        .register-prompt {
            text-align: center;
            margin: 1.8rem 0 1.2rem;
            font-size: 0.95rem;
            color: #3e5e73;
        }
        .register-prompt a {
            font-weight: 700;
            color: #174455;
            text-decoration: none;
            border-bottom: 2px solid #ffdb9f;
        }
        .register-prompt a:hover {
            color: #0b2a36;
        }
        .demo-section {
            margin-top: 1.5rem;
            background: #f0f7fb;
            border-radius: 1.5rem;
            padding: 1.3rem;
        }
        .demo-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            color: #174455;
            margin-bottom: 0.9rem;
        }
        .demo-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.7rem;
        }
        .demo-card {
            background: white;
            border-radius: 1rem;
            padding: 0.6rem 0.8rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
            border: 1px solid #dde9f2;
        }
        .demo-card p {
            font-weight: 600;
            font-size: 0.8rem;
            color: #1f4b5e;
        }
        .demo-card small {
            font-size: 0.7rem;
            color: #537a90;
            display: block;
        }
        .demo-card code {
            background: #174455;
            color: white;
            font-size: 0.65rem;
            padding: 0.2rem 0.4rem;
            border-radius: 1rem;
            margin-top: 0.2rem;
            display: inline-block;
        }
        .status-message {
            background: #dff0d8;
            border-left: 4px solid #3c763d;
            color: #2b5e2b;
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .error-message {
            background: #f2dede;
            border-left: 4px solid #a94442;
            color: #a12b2b;
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        footer {
            margin-top: 1.5rem;
            font-size: 0.75rem;
            color: #7c9bb2;
            text-align: center;
        }
        @media (max-width: 750px) {
            .split-card {
                grid-template-columns: 1fr;
                border-radius: 1.5rem;
            }
            .brand-panel {
                padding: 2rem;
            }
        }
        /* Password requirements styling */
        .password-requirements {
            background: #f0f7fb;
            border-radius: 1rem;
            padding: 0.8rem 1rem;
            margin-top: 0.5rem;
            font-size: 0.75rem;
            color: #345b70;
            border-left: 3px solid #ffdb9f;
        }
        .password-requirements ul {
            list-style: none;
            padding-left: 0;
            margin-top: 0.3rem;
        }
        .password-requirements li {
            display: flex;
            align-items: center;
            gap: 0.3rem;
            margin-bottom: 0.2rem;
        }
        .password-requirements i {
            color: #286b7f;
            font-size: 0.7rem;
        }
    </style>
</head>
<body>
    <div class="split-card">
        <!-- LEFT: BRAND PANEL (exactly same as login) -->
        <div class="brand-panel">
            <div>
                <div class="brand-header">
                    <i class="fas fa-building"></i>
                    <h1>rms<span class="badge">rent & hostel</span></h1>
                </div>
                <div class="hero-text">
                    <p>Join our community.<br>Start your journey.</p>
                    <p><i class="fas fa-map-pin" style="margin-right: 4px;"></i> Apartments · Hostels · PGs</p>
                </div>
                <div class="feature-grid">
                    <div class="feature-item"><i class="fas fa-key"></i><span>Smart room access & billing</span></div>
                    <div class="feature-item"><i class="fas fa-utensils"></i><span>Food & meal plans (hostel ready)</span></div>
                    <div class="feature-item"><i class="fas fa-hand-holding-heart"></i><span>Maintenance requests · 24/7</span></div>
                </div>
            </div>
            <div style="font-size: 0.9rem; opacity: 0.7; display: flex; gap: 1.5rem;">
                <span><i class="far fa-building"></i> 120+ properties</span>
                <span><i class="far fa-smile"></i> 2.4k tenants</span>
            </div>
        </div>

        <!-- RIGHT: REGISTER FORM (same styling as login) -->
        <div class="form-panel" x-data="{ showPassword: false, showConfirmPassword: false }">
            <h2>create account</h2>
            <div class="form-sub">join RMS as a tenant or provider</div>

            <!-- Validation Errors -->
            @if ($errors->any())
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Name field -->
                <div class="input-group">
                    <div class="input-label">
                        <label for="name">Full Name</label>
                    </div>
                    <div class="input-field">
                        <i class="fas fa-user icon-left"></i>
                        <input 
                            id="name" 
                            type="text" 
                            name="name" 
                            value="{{ old('name') }}" 
                            placeholder="John Doe" 
                            required 
                            autofocus 
                            autocomplete="name"
                        >
                    </div>
                    @error('name')
                        <p style="color: #a94442; font-size: 0.75rem; margin-top: 0.3rem; margin-left: 0.5rem;">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Email field -->
                <div class="input-group">
                    <div class="input-label">
                        <label for="email">Email Address</label>
                    </div>
                    <div class="input-field">
                        <i class="fas fa-envelope icon-left"></i>
                        <input 
                            id="email" 
                            type="email" 
                            name="email" 
                            value="{{ old('email') }}" 
                            placeholder="you@example.com" 
                            required 
                            autocomplete="email"
                        >
                    </div>
                    @error('email')
                        <p style="color: #a94442; font-size: 0.75rem; margin-top: 0.3rem; margin-left: 0.5rem;">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Password field with eye toggle -->
                <div class="input-group">
                    <div class="input-label">
                        <label for="password">Password</label>
                    </div>
                    <div class="input-field">
                        <i class="fas fa-lock icon-left"></i>
                        <input 
                            id="password" 
                            :type="showPassword ? 'text' : 'password'" 
                            name="password" 
                            placeholder="••••••••" 
                            required 
                            autocomplete="new-password"
                        >
                        <button 
                            type="button" 
                            @click="showPassword = !showPassword" 
                            class="toggle-pw" 
                            :aria-label="showPassword ? 'Hide password' : 'Show password'"
                        >
                            <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                    </div>
                    @error('password')
                        <p style="color: #a94442; font-size: 0.75rem; margin-top: 0.3rem; margin-left: 0.5rem;">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </p>
                    @enderror
                    <!-- Password requirements -->
                    <div class="password-requirements">
                        <i class="fas fa-shield-alt" style="color: #174455;"></i> Password must:
                        <ul>
                            <li><i class="fas fa-circle" style="font-size: 0.4rem;"></i> Be at least 8 characters</li>
                            <li><i class="fas fa-circle" style="font-size: 0.4rem;"></i> Include uppercase & lowercase</li>
                            <li><i class="fas fa-circle" style="font-size: 0.4rem;"></i> Include numbers & symbols</li>
                        </ul>
                    </div>
                </div>

                <!-- Confirm Password field with eye toggle -->
                <div class="input-group">
                    <div class="input-label">
                        <label for="password_confirmation">Confirm Password</label>
                    </div>
                    <div class="input-field">
                        <i class="fas fa-lock icon-left"></i>
                        <input 
                            id="password_confirmation" 
                            :type="showConfirmPassword ? 'text' : 'password'" 
                            name="password_confirmation" 
                            placeholder="••••••••" 
                            required 
                            autocomplete="new-password"
                        >
                        <button 
                            type="button" 
                            @click="showConfirmPassword = !showConfirmPassword" 
                            class="toggle-pw" 
                            :aria-label="showConfirmPassword ? 'Hide password' : 'Show password'"
                        >
                            <i :class="showConfirmPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <p style="color: #a94442; font-size: 0.75rem; margin-top: 0.3rem; margin-left: 0.5rem;">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Terms and Conditions -->
                <div class="checkbox-row">
                    <input 
                        type="checkbox" 
                        name="terms" 
                        id="terms" 
                        required
                        {{ old('terms') ? 'checked' : '' }}
                    >
                    <label for="terms">
                        I agree to the 
                        <a href="#" style="color: #174455; font-weight: 600;">Terms</a> and 
                        <a href="#" style="color: #174455; font-weight: 600;">Privacy Policy</a>
                    </label>
                </div>
                @error('terms')
                    <p style="color: #a94442; font-size: 0.75rem; margin-top: -0.5rem; margin-bottom: 1rem;">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </p>
                @enderror

                <!-- Submit button -->
                <button type="submit" class="btn-signin">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>

                <!-- Login link -->
                <div class="register-prompt">
                    Already have an account? <a href="{{ route('login') }}">Sign in <i class="fas fa-angle-right"></i></a>
                </div>
            </form>

            <!-- Benefits section (replacing demo accounts) -->
            <div class="demo-section">
                <div class="demo-title">
                    <i class="fas fa-gem"></i> Why join RMS?
                </div>
                <div class="demo-grid">
                    <div class="demo-card">
                        <p><i class="fas fa-home" style="color: #174455;"></i> Find stays</p>
                        <small>Hostels & apartments</small>
                    </div>
                    <div class="demo-card">
                        <p><i class="fas fa-utensils" style="color: #174455;"></i> Meal plans</p>
                        <small>Daily subscriptions</small>
                    </div>
                    <div class="demo-card">
                        <p><i class="fas fa-tshirt" style="color: #174455;"></i> Laundry</p>
                        <small>Pickup & delivery</small>
                    </div>
                    <div class="demo-card">
                        <p><i class="fas fa-store" style="color: #174455;"></i> Be a provider</p>
                        <small>Grow your business</small>
                    </div>
                </div>
            </div>

            <footer>
                © {{ date('Y') }} RMS — apartment & hostel suite
            </footer>
        </div>
    </div>
</body>
</html>