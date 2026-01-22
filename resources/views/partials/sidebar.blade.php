<div class="flex-1 px-4 pt-4 pb-4 space-y-2 overflow-y-auto">
    <!-- Dashboard -->
    <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="fas fa-tachometer-alt"></i>
        <span>Dashboard</span>
    </a>
    
    <!-- Rental -->
    <a href="{{ route('rental.index') }}" class="sidebar-link {{ request()->is('rental*') ? 'active' : '' }}">
        <i class="fas fa-home"></i>
        <span>Rental</span>
    </a>
    
    <!-- Search Rental -->
    <a href="{{ route('rental.search') }}" class="sidebar-link {{ request()->is('rental/search*') ? 'active' : '' }}">
        <i class="fas fa-search"></i>
        <span>Search Rental</span>
    </a>
    
    <!-- Food -->
    <a href="{{ route('food.index') }}" class="sidebar-link {{ request()->is('food*') ? 'active' : '' }}">
        <i class="fas fa-utensils"></i>
        <span>Food</span>
    </a>
    
    <!-- Laundry -->
    <a href="{{ route('laundry.index') }}" class="sidebar-link {{ request()->is('laundry*') ? 'active' : '' }}">
        <i class="fas fa-tshirt"></i>
        <span>Laundry</span>
    </a>
    
    <!-- Payments -->
    <a href="{{ route('payments.index') }}" class="sidebar-link {{ request()->is('payments*') ? 'active' : '' }}">
        <i class="fas fa-credit-card"></i>
        <span>Payments</span>
    </a>
    
    <!-- Apply for Role (Only for regular users) -->
    @if(auth()->user()->hasRole('USER') && !auth()->user()->isOwner() && !auth()->user()->isFoodProvider() && !auth()->user()->isLaundryProvider())
        <a href="{{ route('role.apply.index') }}" class="sidebar-link {{ request()->is('role/apply*') ? 'active' : '' }}">
            <i class="fas fa-user-plus"></i>
            <span>Apply for Role</span>
        </a>
    @endif
    
    <!-- Profile -->
    <a href="{{ route('profile.edit') }}" class="sidebar-link {{ request()->is('profile*') ? 'active' : '' }}">
        <i class="fas fa-user-cog"></i>
        <span>Profile</span>
    </a>
    
    <!-- Role-specific links (if user has a provider role) -->
    @if(auth()->user()->isOwner())
        <div class="pt-4 mt-4 border-t border-gray-200">
            <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                Owner Panel
            </h3>
            <div class="mt-2 space-y-2">
                <a href="{{ route('properties.index') }}" class="sidebar-link">
                    <i class="fas fa-building"></i>
                    <span>My Properties</span>
                </a>
            </div>
        </div>
    @endif
    
    @if(auth()->user()->isFoodProvider())
        <div class="pt-4 mt-4 border-t border-gray-200">
            <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                Food Provider
            </h3>
            <div class="mt-2 space-y-2">
                <a href="#" class="sidebar-link">
                    <i class="fas fa-concierge-bell"></i>
                    <span>Manage Orders</span>
                </a>
            </div>
        </div>
    @endif
    
    @if(auth()->user()->isLaundryProvider())
        <div class="pt-4 mt-4 border-t border-gray-200">
            <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                Laundry Provider
            </h3>
            <div class="mt-2 space-y-2">
                <a href="#" class="sidebar-link">
                    <i class="fas fa-soap"></i>
                    <span>Manage Orders</span>
                </a>
            </div>
        </div>
    @endif
    
    @if(auth()->user()->isSuperAdmin())
        <div class="pt-4 mt-4 border-t border-gray-200">
            <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                Admin Panel
            </h3>
            <div class="mt-2 space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link">
                    <i class="fas fa-cog"></i>
                    <span>Admin Dashboard</span>
                </a>
            </div>
        </div>
    @endif
</div>