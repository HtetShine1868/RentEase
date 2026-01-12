@auth
    @if(! auth()->user()->hasVerifiedEmail())
        <a href="{{ route('verification.notice') }}">Verify Email</a>
    @else
        <a href="{{ route('dashboard') }}">Dashboard</a>
    @endif

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>
@else
    <a href="{{ route('login') }}">Login</a>
    <a href="{{ route('register') }}">Register</a>
@endauth
