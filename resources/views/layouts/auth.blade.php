<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title') | RMS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center">

<div class="w-full max-w-md px-6">
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-semibold text-gray-900">
            Rent & Service Management
        </h1>
        <p class="text-sm text-gray-500 mt-1">
            Simple. Reliable. Unified platform.
        </p>
    </div>

    <div class="bg-white border rounded-xl shadow-sm p-6">
        @yield('content')
    </div>

    <p class="text-center text-xs text-gray-400 mt-6">
        © {{ date('Y') }} RMS. All rights reserved.
    </p>
</div>

<script>
    function togglePassword(inputId, button) {
        const input = document.getElementById(inputId);
        if (input.type === 'password') {
            input.type = 'text';
            button.textContent = '🙈';
        } else {
            input.type = 'password';
            button.textContent = '👁️';
        }
    }
</script>

</body>
</html>
