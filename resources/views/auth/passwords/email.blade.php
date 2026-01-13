<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <style>
        body{ font-family: Arial; background:#f2f2f2; }
        .container{ max-width:400px; margin:100px auto; background:#fff; padding:20px; border-radius:8px; box-shadow:0 0 10px rgba(0,0,0,0.1);}
        input, button{ width:100%; padding:10px; margin:10px 0; border-radius:5px; border:1px solid #ccc;}
        button{ background:#007BFF; color:#fff; border:none; cursor:pointer;}
        button:hover{ background:#0056b3; }
        .status{ color:green; }
        .error{ color:red; }
    </style>
</head>
<body>
<div class="container">
    <h2>Forgot Password</h2>
    @if(session('status'))
        <p class="status">{{ session('status') }}</p>
    @endif
    @if($errors->any())
        <p class="error">{{ $errors->first() }}</p>
    @endif
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <input type="email" name="email" placeholder="Enter your email" required>
        <button type="submit">Send Reset Link</button>
    </form>
    <a href="{{ route('login') }}">Back to Login</a>
</div>
</body>
</html>
