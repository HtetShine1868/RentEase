<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RMS - Rent & Service Management System</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        
        /* RMS Green/Teal Color Scheme */
        :root {
            --primary-dark: #174455;
            --primary: #1f556b;
            --primary-light: #286b7f;
            --accent-gold: #ffdb9f;
            --bg-dark: #0f1f28;
            --bg-card: #1a2f3a;
            --text-light: #f8fafc;
            --text-muted: #94a3b8;
        }
        
        body {
            background-color: var(--bg-dark);
            color: var(--text-light);
        }
        
        .gradient-overlay {
            background: linear-gradient(135deg, rgba(23, 68, 85, 0.95) 0%, rgba(31, 85, 107, 0.9) 100%);
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .hover-lift {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .hover-lift:hover {
            transform: translateY(-4px);
        }
        
        .btn-primary {
            background-color: var(--primary-dark);
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 4px 14px 0 rgba(23, 68, 85, 0.4);
        }
        
        .btn-primary:hover {
            background-color: var(--primary);
            box-shadow: 0 6px 20px rgba(23, 68, 85, 0.5);
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-light);
            transition: all 0.3s ease;
        }
        
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }
        
        .property-card {
            background: var(--bg-card);
            border: 1px solid rgba(40, 107, 127, 0.3);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .property-card:hover {
            border-color: var(--primary-light);
            background: #1f3642;
            transform: translateY(-4px);
            box-shadow: 0 20px 30px -10px rgba(0, 0, 0, 0.5);
        }
        
        .fade-in {
            animation: fadeIn 0.8s ease-out forwards;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .stat-counter {
            font-feature-settings: 'tnum';
            font-variant-numeric: tabular-nums;
        }
        
        .service-icon {
            background: rgba(40, 107, 127, 0.2);
            color: var(--accent-gold);
        }
        
        .accent-text {
            color: var(--accent-gold);
        }
        
        .accent-border {
            border-color: var(--accent-gold);
        }
        
        .gradient-text {
            background: linear-gradient(135deg, #ffdb9f 0%, #f8fafc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body class="min-h-screen">
    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 glass-effect">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <div class="flex items-center gap-3">
                    <svg class="w-8 h-8" style="color: var(--accent-gold);" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="text-xl font-bold tracking-tight" style="color: var(--text-light);">RMS</span>
                </div>
                <div class="hidden md:flex items-center gap-8">
                    <a href="#services" class="text-sm font-medium transition-colors hover:text-white" style="color: var(--text-muted);">Services</a>
                    <a href="#providers" class="text-sm font-medium transition-colors hover:text-white" style="color: var(--text-muted);">For Providers</a>
                    <a href="#how" class="text-sm font-medium transition-colors hover:text-white" style="color: var(--text-muted);">How It Works</a>
                </div>
                <div class="flex items-center gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-secondary px-5 py-2.5 rounded-lg text-sm font-semibold">
                            Dashboard
                        </a>
                    @else
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" class="btn-secondary px-5 py-2.5 rounded-lg text-sm font-semibold">
                                Log In
                            </a>
                        @endif
                        
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-primary px-5 py-2.5 rounded-lg text-sm font-semibold">
                                Get Started
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Hero Section - Full width, no right panel -->
    <section class="relative min-h-screen flex items-center justify-center px-6 pt-20">
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute inset-0" style="background: radial-gradient(circle at 20% 50%, rgba(40, 107, 127, 0.2) 0%, transparent 50%), radial-gradient(circle at 80% 30%, rgba(255, 219, 159, 0.1) 0%, transparent 50%);"></div>
            <div class="absolute top-1/4 left-1/4 w-96 h-96 rounded-full" style="background: radial-gradient(circle, rgba(40, 107, 127, 0.15) 0%, transparent 70%); filter: blur(80px);"></div>
            <div class="absolute bottom-1/4 right-1/4 w-96 h-96 rounded-full" style="background: radial-gradient(circle, rgba(255, 219, 159, 0.1) 0%, transparent 70%); filter: blur(80px);"></div>
        </div>
        <div class="relative max-w-4xl mx-auto w-full">
            <div class="fade-in text-center">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full mb-8 glass-effect mx-auto">
                    <div class="w-2 h-2 rounded-full" style="background: var(--accent-gold);"></div>
                    <span class="text-sm font-medium" style="color: var(--text-muted);">Complete Living Solution</span>
                </div>
                <h1 class="text-5xl lg:text-6xl font-bold leading-tight mb-6 tracking-tight" style="color: var(--text-light);">
                    Rent, Eat, Laundry<br>
                    <span class="gradient-text">
                        All in One Platform
                    </span>
                </h1>
                <p class="text-xl mb-8 leading-relaxed max-w-2xl mx-auto" style="color: var(--text-muted);">
                    Find apartments, order meals, schedule laundry - manage your daily needs seamlessly. For tenants, owners, and service providers.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-8">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-primary px-8 py-4 rounded-xl text-base font-semibold w-full sm:w-auto flex items-center justify-center">
                            Go to Dashboard
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    @else
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-primary px-8 py-4 rounded-xl text-base font-semibold w-full sm:w-auto flex items-center justify-center">
                                Create Free Account
                                <i class="fas fa-arrow-right ml-2"></i>
                            </a>
                        @endif
                        
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" class="btn-secondary px-8 py-4 rounded-xl text-base font-semibold w-full sm:w-auto flex items-center justify-center">
                                <i class="fas fa-sign-in-alt mr-2"></i>
                                Sign In
                            </a>
                        @endif
                    @endauth
                </div>
                <!-- Quick Stats -->
         
            </div>
        </div>
    </section>
    
    <!-- Services Section -->
    <section id="services" class="py-24 px-6 relative">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl lg:text-5xl font-bold mb-4" style="color: var(--text-light);">Our Services</h2>
                <p class="text-lg" style="color: var(--text-muted);">Everything you need in one unified platform</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="property-card p-8 rounded-2xl hover-lift">
                    <div class="w-16 h-16 mb-6 rounded-2xl flex items-center justify-center text-3xl service-icon">
                        <i class="fas fa-home"></i>
                    </div>
                    <h3 class="text-2xl font-semibold mb-3" style="color: var(--text-light);">Accommodation</h3>
                    <p class="mb-4" style="color: var(--text-muted);">Find and book hostels or apartments based on your preferences. Compare prices and secure your space instantly.</p>
                    <ul class="space-y-2">
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check mt-0.5" style="color: var(--accent-gold);"></i>
                            <span class="text-sm" style="color: var(--text-muted);">Location-based search</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check mt-0.5" style="color: var(--accent-gold);"></i>
                            <span class="text-sm" style="color: var(--text-muted);">Instant booking</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check mt-0.5" style="color: var(--accent-gold);"></i>
                            <span class="text-sm" style="color: var(--text-muted);">Real-time availability</span>
                        </li>
                    </ul>
                </div>
                <div class="property-card p-8 rounded-2xl hover-lift">
                    <div class="w-16 h-16 mb-6 rounded-2xl flex items-center justify-center text-3xl service-icon">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <h3 class="text-2xl font-semibold mb-3" style="color: var(--text-light);">Food Services</h3>
                    <p class="mb-4" style="color: var(--text-muted);">Order meals on-demand or subscribe to daily meal plans from verified providers near you.</p>
                    <ul class="space-y-2">
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check mt-0.5" style="color: var(--accent-gold);"></i>
                            <span class="text-sm" style="color: var(--text-muted);">One-time or subscription</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check mt-0.5" style="color: var(--accent-gold);"></i>
                            <span class="text-sm" style="color: var(--text-muted);">Live order tracking</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check mt-0.5" style="color: var(--accent-gold);"></i>
                            <span class="text-sm" style="color: var(--text-muted);">Customizable meals</span>
                        </li>
                    </ul>
                </div>
                <div class="property-card p-8 rounded-2xl hover-lift">
                    <div class="w-16 h-16 mb-6 rounded-2xl flex items-center justify-center text-3xl service-icon">
                        <i class="fas fa-tshirt"></i>
                    </div>
                    <h3 class="text-2xl font-semibold mb-3" style="color: var(--text-light);">Laundry</h3>
                    <p class="mb-4" style="color: var(--text-muted);">Schedule pickup and delivery services. Track your laundry status in real-time.</p>
                    <ul class="space-y-2">
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check mt-0.5" style="color: var(--accent-gold);"></i>
                            <span class="text-sm" style="color: var(--text-muted);">Flexible scheduling</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check mt-0.5" style="color: var(--accent-gold);"></i>
                            <span class="text-sm" style="color: var(--text-muted);">Status notifications</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check mt-0.5" style="color: var(--accent-gold);"></i>
                            <span class="text-sm" style="color: var(--text-muted);">Doorstep delivery</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    
    <!-- For Providers Section -->
    <section id="providers" class="py-24 px-6 relative">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl lg:text-5xl font-bold mb-4" style="color: var(--text-light);">For Service Providers</h2>
                <p class="text-lg" style="color: var(--text-muted);">Grow your business with RMS</p>
            </div>
            <div class="grid md:grid-cols-3 gap-6 mb-16">
                <div class="property-card p-8 rounded-2xl hover-lift">
                    <div class="w-14 h-14 mb-6 rounded-xl flex items-center justify-center text-2xl service-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3" style="color: var(--text-light);">Property Owners</h3>
                    <p class="mb-4" style="color: var(--text-muted);">List and manage your properties with ease. Set prices and connect with verified tenants.</p>
                    <ul class="space-y-2">
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check-circle text-sm" style="color: var(--accent-gold);"></i>
                            <span class="text-sm" style="color: var(--text-muted);">Easy property listing</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check-circle text-sm" style="color: var(--accent-gold);"></i>
                            <span class="text-sm" style="color: var(--text-muted);">Booking management</span>
                        </li>
                    </ul>
                </div>
                <div class="property-card p-8 rounded-2xl hover-lift">
                    <div class="w-14 h-14 mb-6 rounded-xl flex items-center justify-center text-2xl service-icon">
                        <i class="fas fa-store"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3" style="color: var(--text-light);">Food Providers</h3>
                    <p class="mb-4" style="color: var(--text-muted);">Manage your menu, process orders, and track deliveries all in one place.</p>
                    <ul class="space-y-2">
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check-circle text-sm" style="color: var(--accent-gold);"></i>
                            <span class="text-sm" style="color: var(--text-muted);">Menu management</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check-circle text-sm" style="color: var(--accent-gold);"></i>
                            <span class="text-sm" style="color: var(--text-muted);">Order notifications</span>
                        </li>
                    </ul>
                </div>
                <div class="property-card p-8 rounded-2xl hover-lift">
                    <div class="w-14 h-14 mb-6 rounded-xl flex items-center justify-center text-2xl service-icon">
                        <i class="fas fa-soap"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3" style="color: var(--text-light);">Laundry Providers</h3>
                    <p class="mb-4" style="color: var(--text-muted);">Handle service requests efficiently with automated scheduling.</p>
                    <ul class="space-y-2">
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check-circle text-sm" style="color: var(--accent-gold);"></i>
                            <span class="text-sm" style="color: var(--text-muted);">Pickup scheduling</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check-circle text-sm" style="color: var(--accent-gold);"></i>
                            <span class="text-sm" style="color: var(--text-muted);">Status updates</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Features -->
            <div class="property-card p-12 rounded-3xl">
                <div class="text-center mb-12">
                    <h3 class="text-3xl font-bold mb-4" style="color: var(--text-light);">Why Choose RMS</h3>
                </div>
                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div class="text-center">
                        <div class="w-12 h-12 mx-auto mb-4 rounded-xl flex items-center justify-center service-icon">
                            <i class="fas fa-percent"></i>
                        </div>
                        <h4 class="font-semibold mb-2" style="color: var(--text-light);">Fair Commission</h4>
                        <p class="text-sm" style="color: var(--text-muted);">Transparent pricing</p>
                    </div>
                    <div class="text-center">
                        <div class="w-12 h-12 mx-auto mb-4 rounded-xl flex items-center justify-center service-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <h4 class="font-semibold mb-2" style="color: var(--text-light);">Smart Notifications</h4>
                        <p class="text-sm" style="color: var(--text-muted);">Real-time alerts</p>
                    </div>
                    <div class="text-center">
                        <div class="w-12 h-12 mx-auto mb-4 rounded-xl flex items-center justify-center service-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4 class="font-semibold mb-2" style="color: var(--text-light);">Analytics</h4>
                        <p class="text-sm" style="color: var(--text-muted);">Track performance</p>
                    </div>
                    <div class="text-center">
                        <div class="w-12 h-12 mx-auto mb-4 rounded-xl flex items-center justify-center service-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <h4 class="font-semibold mb-2" style="color: var(--text-light);">24/7 Support</h4>
                        <p class="text-sm" style="color: var(--text-muted);">Always here to help</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- CTA Section -->
    <section class="py-24 px-6">
        <div class="max-w-4xl mx-auto">
            <div class="property-card p-12 lg:p-16 rounded-3xl text-center relative overflow-hidden">
                <div class="absolute inset-0" style="background: linear-gradient(135deg, rgba(23, 68, 85, 0.3) 0%, rgba(40, 107, 127, 0.3) 100%);"></div>
                <div class="relative">
                    <h2 class="text-4xl lg:text-5xl font-bold mb-6" style="color: var(--text-light);">Ready to Get Started?</h2>
                    <p class="text-xl mb-10 max-w-2xl mx-auto" style="color: var(--text-muted);">Join RMS today. Whether you're looking for services or want to become a provider, we've got you covered.</p>
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn-primary px-10 py-4 rounded-xl text-base font-semibold w-full sm:w-auto">
                                Go to Dashboard
                            </a>
                        @else
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn-primary px-10 py-4 rounded-xl text-base font-semibold w-full sm:w-auto">
                                    Get Started Free
                                </a>
                            @endif
                            
                            @if (Route::has('login'))
                                <a href="{{ route('login') }}" class="btn-secondary px-10 py-4 rounded-xl text-base font-semibold w-full sm:w-auto">
                                    Sign In
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <footer class="py-16 px-6" style="border-top: 1px solid rgba(255, 255, 255, 0.1);">
        <div class="max-w-7xl mx-auto">
            <div class="grid md:grid-cols-4 gap-12 mb-12">
                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <svg class="w-8 h-8" style="color: var(--accent-gold);" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span class="text-xl font-bold" style="color: var(--text-light);">RMS</span>
                    </div>
                    <p style="color: var(--text-muted);">Your complete living solution for accommodation, food, and laundry services.</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4" style="color: var(--text-light);">Company</h4>
                    <ul class="space-y-3">
                        <li><a href="#" style="color: var(--text-muted);" class="hover:text-white transition">About Us</a></li>
                        <li><a href="#" style="color: var(--text-muted);" class="hover:text-white transition">Contact</a></li>
                        <li><a href="#" style="color: var(--text-muted);" class="hover:text-white transition">Blog</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4" style="color: var(--text-light);">Support</h4>
                    <ul class="space-y-3">
                        <li><a href="#" style="color: var(--text-muted);" class="hover:text-white transition">Help Center</a></li>
                        <li><a href="#" style="color: var(--text-muted);" class="hover:text-white transition">FAQ</a></li>
                        <li><a href="#" style="color: var(--text-muted);" class="hover:text-white transition">Safety</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4" style="color: var(--text-light);">Legal</h4>
                    <ul class="space-y-3">
                        <li><a href="#" style="color: var(--text-muted);" class="hover:text-white transition">Privacy</a></li>
                        <li><a href="#" style="color: var(--text-muted);" class="hover:text-white transition">Terms</a></li>
                        <li><a href="#" style="color: var(--text-muted);" class="hover:text-white transition">Cookies</a></li>
                    </ul>
                </div>
            </div>
            <div class="pt-8 flex flex-col md:flex-row items-center justify-between" style="border-top: 1px solid rgba(255, 255, 255, 0.1);">
                <p style="color: var(--text-muted);">© {{ date('Y') }} RMS Platform. All rights reserved.</p>
                <div class="flex items-center gap-6 mt-4 md:mt-0">
                    <a href="#" class="hover:text-white transition" style="color: var(--text-muted);"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="hover:text-white transition" style="color: var(--text-muted);"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="hover:text-white transition" style="color: var(--text-muted);"><i class="fab fa-facebook"></i></a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>