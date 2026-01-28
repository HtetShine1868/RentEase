<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>RMS - Rent & Service Management System</title>
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        
        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
        
        <style>
            * {
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            }
            
            .gradient-overlay {
                background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.9) 100%);
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
                transition: all 0.3s ease;
                box-shadow: 0 4px 14px 0 rgba(99, 102, 241, 0.39);
            }
            
            .btn-primary:hover {
                box-shadow: 0 6px 20px rgba(99, 102, 241, 0.5);
                transform: translateY(-2px);
            }
            
            .btn-secondary {
                transition: all 0.3s ease;
            }
            
            .btn-secondary:hover {
                background: rgba(255, 255, 255, 0.15);
                transform: translateY(-2px);
            }
            
            .property-card {
                background: linear-gradient(145deg, rgba(255,255,255,0.08) 0%, rgba(255,255,255,0.04) 100%);
                border: 1px solid rgba(255, 255, 255, 0.1);
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            }
            
            .property-card:hover {
                border-color: rgba(99, 102, 241, 0.5);
                background: linear-gradient(145deg, rgba(255,255,255,0.12) 0%, rgba(255,255,255,0.06) 100%);
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
            
            .dark-mode {
                background-color: #0f172a;
                color: #f8fafc;
            }
            
            .light-mode {
                background-color: #ffffff;
                color: #1f2937;
            }
        </style>
    </head>
    <body class="dark-mode min-h-screen">
        <!-- Navigation -->
        <nav class="fixed top-0 left-0 right-0 z-50 glass-effect">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="flex items-center justify-between h-20">
                    <div class="flex items-center gap-3">
                        <svg class="w-8 h-8" style="color: #6366f1;" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span class="text-xl font-bold tracking-tight" style="color: #f8fafc;">RMS</span>
                    </div>
                    <div class="hidden md:flex items-center gap-8">
                        <a href="#services" class="text-sm font-medium transition-colors hover:text-white" style="color: #cbd5e1;">Services</a>
                        <a href="#providers" class="text-sm font-medium transition-colors hover:text-white" style="color: #cbd5e1;">For Providers</a>
                        <a href="#how" class="text-sm font-medium transition-colors hover:text-white" style="color: #cbd5e1;">How It Works</a>
                    </div>
                    <div class="flex items-center gap-4">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn-secondary px-5 py-2.5 rounded-lg text-sm font-semibold" style="color: #f8fafc; background: rgba(255, 255, 255, 0.1);">
                                Dashboard
                            </a>
                        @else
                            @if (Route::has('login'))
                                <a href="{{ route('login') }}" class="btn-secondary px-5 py-2.5 rounded-lg text-sm font-semibold" style="color: #f8fafc; background: rgba(255, 255, 255, 0.1);">
                                    Log In
                                </a>
                            @endif
                            
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn-primary px-5 py-2.5 rounded-lg text-sm font-semibold" style="background: #6366f1; color: #ffffff;">
                                    Get Started
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </nav>
        
        <!-- Hero Section -->
        <section class="relative min-h-screen flex items-center justify-center px-6 pt-20">
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute inset-0" style="background: radial-gradient(circle at 20% 50%, rgba(99, 102, 241, 0.15) 0%, transparent 50%), radial-gradient(circle at 80% 30%, rgba(147, 51, 234, 0.1) 0%, transparent 50%);"></div>
                <div class="absolute top-1/4 left-1/4 w-96 h-96 rounded-full" style="background: radial-gradient(circle, rgba(99, 102, 241, 0.1) 0%, transparent 70%); filter: blur(80px);"></div>
                <div class="absolute bottom-1/4 right-1/4 w-96 h-96 rounded-full" style="background: radial-gradient(circle, rgba(147, 51, 234, 0.1) 0%, transparent 70%); filter: blur(80px);"></div>
            </div>
            <div class="relative max-w-7xl mx-auto w-full">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <!-- Left Content -->
                    <div class="fade-in">
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full mb-8 glass-effect">
                            <div class="w-2 h-2 rounded-full" style="background: #6366f1;"></div>
                            <span class="text-sm font-medium" style="color: #cbd5e1;">One Platform, All Your Essential Services</span>
                        </div>
                        <h1 class="text-5xl lg:text-6xl font-bold leading-tight mb-6 tracking-tight" style="color: #f8fafc;">
                            Rent, Eat, Laundry<br>
                            <span style="background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                                All in One Platform
                            </span>
                        </h1>
                        <p class="text-xl mb-8 leading-relaxed" style="color: #94a3b8;">
                            Find apartments, order meals, schedule laundry - manage your daily needs seamlessly. For users and service providers.
                        </p>
                        <div class="flex flex-col sm:flex-row items-start gap-4 mb-8">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="btn-primary px-8 py-4 rounded-xl text-base font-semibold w-full sm:w-auto flex items-center justify-center" style="background: #6366f1; color: #ffffff;">
                                    Go to Dashboard
                                    <svg class="inline-block w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    </svg>
                                </a>
                            @else
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="btn-primary px-8 py-4 rounded-xl text-base font-semibold w-full sm:w-auto flex items-center justify-center" style="background: #6366f1; color: #ffffff;">
                                        Create Free Account
                                        <svg class="inline-block w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                        </svg>
                                    </a>
                                @endif
                                
                                @if (Route::has('login'))
                                    <a href="{{ route('login') }}" class="btn-secondary px-8 py-4 rounded-xl text-base font-semibold w-full sm:w-auto flex items-center justify-center" style="color: #f8fafc; background: rgba(255, 255, 255, 0.1);">
                                        Sign In to Your Account
                                    </a>
                                @endif
                            @endauth
                        </div>
                        <!-- Quick Stats -->
                        <div class="flex items-center gap-8">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5" style="color: #6366f1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span class="text-sm font-medium" style="color: #cbd5e1;">Multi-Service</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5" style="color: #6366f1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                                <span class="text-sm font-medium" style="color: #cbd5e1;">Real-Time Tracking</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Content - Visual Card -->
                    <div class="fade-in lg:block hidden">
                        <div class="property-card p-8 rounded-3xl">
                            <div class="space-y-6">
                                <!-- Service Tabs -->
                                <div class="grid grid-cols-3 gap-2 p-1 rounded-xl" style="background: rgba(255, 255, 255, 0.05);">
                                    <button class="px-4 py-3 rounded-lg font-semibold text-sm transition-all" style="background: #6366f1; color: #ffffff;"> 🏠 Rent </button>
                                    <button class="px-4 py-3 rounded-lg font-semibold text-sm transition-all hover:bg-white/5" style="color: #cbd5e1;"> 🍕 Food </button>
                                    <button class="px-4 py-3 rounded-lg font-semibold text-sm transition-all hover:bg-white/5" style="color: #cbd5e1;"> 👕 Laundry </button>
                                </div>
                                
                                <!-- Search Preview -->
                                <div>
                                    <label class="text-sm font-medium mb-2 block" style="color: #cbd5e1;">Location</label>
                                    <div class="glass-effect p-4 rounded-xl flex items-center gap-3">
                                        <svg class="w-5 h-5" style="color: #6366f1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span style="color: #94a3b8;">Enter your location...</span>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-sm font-medium mb-2 block" style="color: #cbd5e1;">Move-in</label>
                                        <div class="glass-effect p-4 rounded-xl flex items-center gap-3">
                                            <svg class="w-5 h-5" style="color: #6366f1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span style="color: #94a3b8;">Date</span>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium mb-2 block" style="color: #cbd5e1;">Budget</label>
                                        <div class="glass-effect p-4 rounded-xl flex items-center gap-3">
                                            <svg class="w-5 h-5" style="color: #6366f1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span style="color: #94a3b8;">Range</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <button class="btn-primary w-full py-4 rounded-xl font-semibold" style="background: #6366f1; color: #ffffff;"> Search Services </button>
                                
                                <!-- Stats -->
                                <div class="grid grid-cols-3 gap-4 pt-6" style="border-top: 1px solid rgba(255, 255, 255, 0.1);">
                                    <div class="text-center">
                                        <div class="stat-counter text-2xl font-bold mb-1" style="color: #6366f1;">5K+</div>
                                        <div class="text-xs" style="color: #64748b;">Properties</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="stat-counter text-2xl font-bold mb-1" style="color: #6366f1;">800+</div>
                                        <div class="text-xs" style="color: #64748b;">Vendors</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="stat-counter text-2xl font-bold mb-1" style="color: #6366f1;">24/7</div>
                                        <div class="text-xs" style="color: #64748b;">Support</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- How It Works Section -->
        <section id="services" class="py-24 px-6 relative">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-4xl lg:text-5xl font-bold mb-4" style="color: #f8fafc;">Three Essential Services</h2>
                    <p class="text-lg" style="color: #94a3b8;">Everything you need in one unified platform</p>
                </div>
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="property-card p-8 rounded-2xl hover-lift">
                        <div class="w-16 h-16 mb-6 rounded-2xl flex items-center justify-center text-4xl" style="background: rgba(99, 102, 241, 0.1);">
                            🏠
                        </div>
                        <h3 class="text-2xl font-semibold mb-3" style="color: #f8fafc;">Accommodation Rental</h3>
                        <p class="mb-4" style="color: #94a3b8;">Search and book hostels or apartments based on your location and preferences. View detailed listings, compare prices, and secure your perfect living space instantly.</p>
                        <ul class="space-y-2">
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 mt-0.5" style="color: #6366f1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-sm" style="color: #cbd5e1;">Location-based search</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 mt-0.5" style="color: #6366f1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-sm" style="color: #cbd5e1;">Instant booking confirmation</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 mt-0.5" style="color: #6366f1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-sm" style="color: #cbd5e1;">Real-time availability</span>
                            </li>
                        </ul>
                    </div>
                    <div class="property-card p-8 rounded-2xl hover-lift">
                        <div class="w-16 h-16 mb-6 rounded-2xl flex items-center justify-center text-4xl" style="background: rgba(99, 102, 241, 0.1);">
                            🍕
                        </div>
                        <h3 class="text-2xl font-semibold mb-3" style="color: #f8fafc;">Food & Meal Plans</h3>
                        <p class="mb-4" style="color: #94a3b8;">Order food on-demand or subscribe to daily meal plans from verified providers. Track your orders in real-time and enjoy hassle-free delivery to your location.</p>
                        <ul class="space-y-2">
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 mt-0.5" style="color: #6366f1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-sm" style="color: #cbd5e1;">One-time or subscription orders</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 mt-0.5" style="color: #6366f1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-sm" style="color: #cbd5e1;">Live order tracking</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 mt-0.5" style="color: #6366f1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-sm" style="color: #cbd5e1;">Customizable meal preferences</span>
                            </li>
                        </ul>
                    </div>
                    <div class="property-card p-8 rounded-2xl hover-lift">
                        <div class="w-16 h-16 mb-6 rounded-2xl flex items-center justify-center text-4xl" style="background: rgba(99, 102, 241, 0.1);">
                            👕
                        </div>
                        <h3 class="text-2xl font-semibold mb-3" style="color: #f8fafc;">Laundry Services</h3>
                        <p class="mb-4" style="color: #94a3b8;">Schedule laundry pickup and delivery based on your location. Get notifications for pickup, cleaning progress, and delivery status all in one place.</p>
                        <ul class="space-y-2">
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 mt-0.5" style="color: #6366f1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-sm" style="color: #cbd5e1;">Flexible pickup scheduling</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 mt-0.5" style="color: #6366f1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-sm" style="color: #cbd5e1;">Status notifications</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 mt-0.5" style="color: #6366f1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-sm" style="color: #cbd5e1;">Doorstep delivery</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Testimonials Section -->
        <section id="providers" class="py-24 px-6 relative">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-4xl lg:text-5xl font-bold mb-4" style="color: #f8fafc;">For Service Providers</h2>
                    <p class="text-lg" style="color: #94a3b8;">Grow your business with RMS - transparent, efficient, and reliable</p>
                </div>
                <div class="grid md:grid-cols-3 gap-6 mb-16">
                    <div class="property-card p-8 rounded-2xl hover-lift">
                        <div class="w-14 h-14 mb-6 rounded-xl flex items-center justify-center text-3xl" style="background: rgba(99, 102, 241, 0.1);">
                            🏢
                        </div>
                        <h3 class="text-xl font-semibold mb-3" style="color: #f8fafc;">Property Owners</h3>
                        <p class="mb-4" style="color: #94a3b8;">List and manage your properties with ease. Set your own prices, manage availability, and connect with verified tenants.</p>
                        <ul class="space-y-2">
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 mt-0.5" style="color: #6366f1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-sm" style="color: #cbd5e1;">Easy property listing</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 mt-0.5" style="color: #6366f1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-sm" style="color: #cbd5e1;">Real-time booking updates</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 mt-0.5" style="color: #6366f1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-sm" style="color: #cbd5e1;">Dashboard analytics</span>
                            </li>
                        </ul>
                    </div>
                    <div class="property-card p-8 rounded-2xl hover-lift">
                        <div class="w-14 h-14 mb-6 rounded-xl flex items-center justify-center text-3xl" style="background: rgba(99, 102, 241, 0.1);">
                            🍽️
                        </div>
                        <h3 class="text-xl font-semibold mb-3" style="color: #f8fafc;">Food Providers</h3>
                        <p class="mb-4" style="color: #94a3b8;">Manage your menu, process orders, and track deliveries all in one place. Accept one-time orders or recurring meal subscriptions.</p>
                        <ul class="space-y-2">
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 mt-0.5" style="color: #6366f1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-sm" style="color: #cbd5e1;">Dynamic menu management</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 mt-0.5" style="color: #6366f1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-sm" style="color: #cbd5e1;">Order notifications</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 mt-0.5" style="color: #6366f1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-sm" style="color: #cbd5e1;">Subscription tracking</span>
                            </li>
                        </ul>
                    </div>
                    <div class="property-card p-8 rounded-2xl hover-lift">
                        <div class="w-14 h-14 mb-6 rounded-xl flex items-center justify-center text-3xl" style="background: rgba(99, 102, 241, 0.1);">
                            🧺
                        </div>
                        <h3 class="text-xl font-semibold mb-3" style="color: #f8fafc;">Laundry Providers</h3>
                        <p class="mb-4" style="color: #94a3b8;">Handle service requests efficiently with automated scheduling and status updates. Keep customers informed every step of the way.</p>
                        <ul class="space-y-2">
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 mt-0.5" style="color: #6366f1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-sm" style="color: #cbd5e1;">Pickup scheduling</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 mt-0.5" style="color: #6366f1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-sm" style="color: #cbd5e1;">Status management</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 mt-0.5" style="color: #6366f1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-sm" style="color: #cbd5e1;">Route optimization</span>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- Platform Features -->
                <div class="property-card p-12 rounded-3xl">
                    <div class="text-center mb-12">
                        <h3 class="text-3xl font-bold mb-4" style="color: #f8fafc;">Why Providers Choose RMS</h3>
                        <p class="text-lg" style="color: #94a3b8;">A reliable platform that helps your business thrive</p>
                    </div>
                    <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                        <div class="text-center">
                            <div class="w-12 h-12 mx-auto mb-4 rounded-xl flex items-center justify-center" style="background: rgba(99, 102, 241, 0.1);">
                                <svg class="w-6 h-6" style="color: #6366f1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h4 class="font-semibold mb-2" style="color: #f8fafc;">Fair Commission</h4>
                            <p class="text-sm" style="color: #94a3b8;">Transparent pricing with competitive rates</p>
                        </div>
                        <div class="text-center">
                            <div class="w-12 h-12 mx-auto mb-4 rounded-xl flex items-center justify-center" style="background: rgba(99, 102, 241, 0.1);">
                                <svg class="w-6 h-6" style="color: #6366f1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            </div>
                            <h4 class="font-semibold mb-2" style="color: #f8fafc;">Smart Notifications</h4>
                            <p class="text-sm" style="color: #94a3b8;">Real-time alerts for new requests</p>
                        </div>
                        <div class="text-center">
                            <div class="w-12 h-12 mx-auto mb-4 rounded-xl flex items-center justify-center" style="background: rgba(99, 102, 241, 0.1);">
                                <svg class="w-6 h-6" style="color: #6366f1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <h4 class="font-semibold mb-2" style="color: #f8fafc;">Analytics Dashboard</h4>
                            <p class="text-sm" style="color: #94a3b8;">Track performance and earnings</p>
                        </div>
                        <div class="text-center">
                            <div class="w-12 h-12 mx-auto mb-4 rounded-xl flex items-center justify-center" style="background: rgba(99, 102, 241, 0.1);">
                                <svg class="w-6 h-6" style="color: #6366f1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <h4 class="font-semibold mb-2" style="color: #f8fafc;">24/7 Support</h4>
                            <p class="text-sm" style="color: #94a3b8;">Always here to help you succeed</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- CTA Section -->
        <section class="py-24 px-6">
            <div class="max-w-4xl mx-auto">
                <div class="property-card p-12 lg:p-16 rounded-3xl text-center relative overflow-hidden">
                    <div class="absolute inset-0" style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(147, 51, 234, 0.1) 100%);"></div>
                    <div class="relative">
                        <h2 class="text-4xl lg:text-5xl font-bold mb-6" style="color: #f8fafc;">Ready to Get Started?</h2>
                        <p class="text-xl mb-10 max-w-2xl mx-auto" style="color: #94a3b8;">Join RMS today. Whether you're looking for services or want to become a provider, we've got you covered.</p>
                        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="btn-primary px-10 py-4 rounded-xl text-base font-semibold w-full sm:w-auto" style="background: #6366f1; color: #ffffff;">
                                    Go to Dashboard
                                </a>
                            @else
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="btn-primary px-10 py-4 rounded-xl text-base font-semibold w-full sm:w-auto" style="background: #6366f1; color: #ffffff;">
                                        Get Started Free
                                    </a>
                                @endif
                                
                                @if (Route::has('login'))
                                    <a href="{{ route('login') }}" class="btn-secondary px-10 py-4 rounded-xl text-base font-semibold w-full sm:w-auto" style="color: #f8fafc; background: rgba(255, 255, 255, 0.1);">
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
                            <svg class="w-8 h-8" style="color: #6366f1;" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span class="text-xl font-bold" style="color: #f8fafc;">RMS</span>
                        </div>
                        <p style="color: #64748b;">Rent & Service Management System - Your one-stop platform for accommodation, food, and laundry services.</p>
                    </div>
                    <div>
                        <h4 class="font-semibold mb-4" style="color: #f8fafc;">Company</h4>
                        <ul class="space-y-3">
                            <li><a href="#" class="transition-colors hover:text-gray-300" style="color: #64748b;">About Us</a></li>
                            <li><a href="#" class="transition-colors hover:text-gray-300" style="color: #64748b;">Careers</a></li>
                            <li><a href="#" class="transition-colors hover:text-gray-300" style="color: #64748b;">Press</a></li>
                            <li><a href="#" class="transition-colors hover:text-gray-300" style="color: #64748b;">Blog</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold mb-4" style="color: #f8fafc;">Support</h4>
                        <ul class="space-y-3">
                            <li><a href="#" class="transition-colors hover:text-gray-300" style="color: #64748b;">Help Center</a></li>
                            <li><a href="#" class="transition-colors hover:text-gray-300" style="color: #64748b;">Safety</a></li>
                            <li><a href="#" class="transition-colors hover:text-gray-300" style="color: #64748b;">Contact</a></li>
                            <li><a href="#" class="transition-colors hover:text-gray-300" style="color: #64748b;">FAQ</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold mb-4" style="color: #f8fafc;">Legal</h4>
                        <ul class="space-y-3">
                            <li><a href="#" class="transition-colors hover:text-gray-300" style="color: #64748b;">Privacy</a></li>
                            <li><a href="#" class="transition-colors hover:text-gray-300" style="color: #64748b;">Terms</a></li>
                            <li><a href="#" class="transition-colors hover:text-gray-300" style="color: #64748b;">Cookies</a></li>
                            <li><a href="#" class="transition-colors hover:text-gray-300" style="color: #64748b;">Licenses</a></li>
                        </ul>
                    </div>
                </div>
                <div class="pt-8 flex flex-col md:flex-row items-center justify-between" style="border-top: 1px solid rgba(255, 255, 255, 0.1);">
                    <p style="color: #64748b;">© 2024 RMS Platform. All rights reserved.</p>
                    <div class="flex items-center gap-6 mt-4 md:mt-0">
                        <a href="#" target="_blank" rel="noopener noreferrer" class="transition-colors hover:text-gray-300" style="color: #64748b;">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z" />
                            </svg>
                        </a>
                        <a href="#" target="_blank" rel="noopener noreferrer" class="transition-colors hover:text-gray-300" style="color: #64748b;">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                            </svg>
                        </a>
                        <a href="#" target="_blank" rel="noopener noreferrer" class="transition-colors hover:text-gray-300" style="color: #64748b;">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </footer>
    </body>
</html>