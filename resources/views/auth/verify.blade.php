<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Email - RMS</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; text-align: center; background: #f3f4f6; }
        .container { max-width: 400px; margin: 50px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .code-input { 
            font-size: 24px; 
            padding: 15px; 
            width: 200px; 
            text-align: center;
            letter-spacing: 10px;
            margin: 20px 0;
            border: 2px solid #d1d5db;
            border-radius: 8px;
        }
        .code-input:focus { 
            border-color: #4f46e5; 
            outline: none;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }
        .btn { 
            padding: 12px 24px; 
            background: #4f46e5; 
            color: white; 
            border: none; 
            border-radius: 8px; 
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-top: 10px;
        }
        .btn:hover { background: #4338ca; }
        .btn-resend { 
            background: #6b7280; 
            margin-top: 20px;
        }
        .btn-resend:hover { background: #4b5563; }
        .error { color: #dc2626; background: #fee2e2; padding: 10px; border-radius: 6px; margin: 15px 0; }
        .success { color: #059669; background: #d1fae5; padding: 10px; border-radius: 6px; margin: 15px 0; }
        .info-box { background: #dbeafe; padding: 15px; border-radius: 8px; margin: 20px 0; text-align: left; }
        .info-box h3 { margin-top: 0; color: #1e40af; }
    </style>
</head>
<body>
    <div class="container">
        <h1 style="color: #4f46e5;">Verify Your Email</h1>
        
        <p>A 6-digit verification code has been sent to:</p>
        <p style="font-weight: bold; font-size: 18px;">{{ $email }}</p>
        
        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif
        
        @if(session('error'))
            <div class="error">{{ session('error') }}</div>
        @endif
        
        <div class="info-box">
            <h3>📧 Check Your Email</h3>
            <p>Look for an email from RMS with the subject "RMS Verification Code"</p>
            <p>If you don't see it, check your spam folder.</p>
        </div>
        
        <form method="POST" action="{{ url('/') }}">
            @csrf
            <input type="text" 
                   name="code" 
                   class="code-input" 
                   maxlength="6" 
                   placeholder="000000"
                   required
                   autofocus>
            <br>
            <button type="submit" class="btn">Verify Email</button>
        </form>
        
        <p style="margin: 20px 0; color: #6b7280;">Didn't receive the code?</p>

        <form method="POST" action="{{ url('/resend') }}">
            @csrf
            <button type="submit" class="btn btn-resend">Resend Verification Code</button>
        </form>
        
        <p style="margin-top: 30px; color: #6b7280;">
            <a href="{{ route('logout') }}" 
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
               style="color: #4f46e5; text-decoration: none;">
                ↻ Use different account
            </a>
        </p>
        
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
    
    <script>
        // Auto-focus the input
        document.querySelector('.code-input').focus();
        
        // Auto-format: only numbers
        document.querySelector('.code-input').addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '');
            
            // Auto-submit when 6 digits entered
            if (this.value.length === 6) {
                this.form.submit();
            }
        });
        
        // Prevent non-numeric input
        document.querySelector('.code-input').addEventListener('keypress', function(e) {
            if (!/[0-9]/.test(e.key)) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>