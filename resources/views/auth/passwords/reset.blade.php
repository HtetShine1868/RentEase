<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <style>
        body{ font-family: Arial; background:#f2f2f2; }
        .container{ max-width:400px; margin:100px auto; background:#fff; padding:20px; border-radius:8px; box-shadow:0 0 10px rgba(0,0,0,0.1);}
        input, button{ width:100%; padding:10px; margin:10px 0; border-radius:5px; border:1px solid #ccc;}
        button{ background:#28a745; color:#fff; border:none; cursor:pointer;}
        button:hover{ background:#218838; }
        .status{ color:green; }
        .error{ color:red; }
        .toggle-btn{ position:absolute; right:20px; top:42px; cursor:pointer; background:none; border:none;}
        .input-wrapper{ position:relative; }
    </style>
</head>
<body>
<div class="container">
    <h2>Reset Password</h2>
    @if($errors->any())
        <p class="error">{{ $errors->first() }}</p>
    @endif
    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" required>

        <div class="input-wrapper">
            <input type="password" id="password" name="password" placeholder="New Password" required>
            <button type="button" class="toggle-btn" onclick="togglePassword()">👁️</button>
        </div>

        <div class="input-wrapper">
            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password" required>
            <button type="button" class="toggle-btn" onclick="togglePasswordConfirm()">👁️</button>
        </div>

        <button type="submit">Reset Password</button>
    </form>
    <a href="{{ route('login') }}">Back to Login</a>
</div>

<script>
function togglePassword() {
  const pass = document.getElementById('password');
  pass.type = pass.type === 'password' ? 'text' : 'password';
}

function togglePasswordConfirm() {
  const pass = document.getElementById('password_confirmation');
  pass.type = pass.type === 'password' ? 'text' : 'password';
}
</script>
</body>
</html>
