<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Email - RMS</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            padding: 20px; 
            text-align: center; 
            background: #f3f4f6; 
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container { 
            max-width: 400px; 
            width: 100%;
            margin: 0 auto; 
            background: white; 
            padding: 30px; 
            border-radius: 10px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
        }
        h1 {
            color: #4f46e5;
            margin-bottom: 20px;
            font-size: 24px;
        }
        .code-input { 
            font-size: 24px; 
            padding: 15px; 
            width: 200px; 
            text-align: center;
            letter-spacing: 10px;
            margin: 20px 0;
            border: 2px solid #d1d5db;
            border-radius: 8px;
            font-weight: bold;
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
            font-weight: 500;
            transition: background 0.2s;
        }
        .btn:hover { 
            background: #4338ca; 
        }
        .btn-resend { 
            background: #6b7280; 
            margin-top: 20px;
        }
        .btn-resend:hover { 
            background: #4b5563; 
        }
        .error { 
            color: #dc2626; 
            background: #fee2e2; 
            padding: 12px; 
            border-radius: 6px; 
            margin: 15px 0; 
            font-size: 14px;
        }
        .success { 
            color: #059669; 
            background: #d1fae5; 
            padding: 12px; 
            border-radius: 6px; 
            margin: 15px 0; 
            font-size: 14px;
        }
        .info-box { 
            background: #dbeafe; 
            padding: 15px; 
            border-radius: 8px; 
            margin: 20px 0; 
            text-align: left; 
        }
        .info-box h3 { 
            margin-top: 0; 
            color: #1e40af; 
            font-size: 16px;
            margin-bottom: 10px;
        }
        .info-box p {
            margin: 5px 0;
            color: #1e3a8a;
            font-size: 14px;
        }
        .email-display {
            font-weight: bold; 
            font-size: 18px; 
            color: #4f46e5;
            background: #eef2ff;
            padding: 10px;
            border-radius: 6px;
            margin: 10px 0;
        }
        .links {
            margin-top: 30px; 
            color: #6b7280;
            font-size: 14px;
        }
        .links a {
            color: #4f46e5; 
            text-decoration: none;
            font-weight: 500;
        }
        .links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
        <div style="background: #000; color: #fff; padding: 20px; margin: 20px; text-align: left; font-family: monospace;">
        <h3 style="color: #ff0;">🔍 ROUTE DEBUG INFO</h3>
        <?php
        echo "<p><strong>route('verification.verify'):</strong> " . var_export(route('verification.verify', [], false), true) . "</p>";
        echo "<p><strong>url('/verify'):</strong> " . url('/verify') . "</p>";
        echo "<p><strong>URL::to('/verify'):</strong> " . URL::to('/verify') . "</p>";
        echo "<p><strong>Request::url():</strong> " . request()->url() . "</p>";
        echo "<p><strong>Request::path():</strong> " . request()->path() . "</p>";
        echo "<p><strong>Session email:</strong> " . (session('verifying_user_email') ?? 'null') . "</p>";
        echo "<p><strong>Auth check:</strong> " . (Auth::check() ? 'true' : 'false') . "</p>";
        ?>
    </div>
    <div class="container">
        <h1>Verify Your Email</h1>
        
        <p>A 6-digit verification code has been sent to:</p>
        <div class="email-display">{{ $email }}</div>
        
        @if(session('success'))
            <div class="success">
                <strong>Success!</strong> {{ session('success') }}
            </div>
        @endif
        
        @if(session('error'))
            <div class="error">
                <strong>Error!</strong> {{ session('error') }}
            </div>
        @endif
        
        <div class="info-box">
            <h3>📧 Check Your Email</h3>
            <p>Look for an email from RMS with the subject "RMS Verification Code"</p>
            <p>If you don't see it, check your spam folder.</p>
            <p style="margin-top: 10px; font-size: 12px; color: #1e40af;">
                Code expires in 10 minutes
            </p>
        </div>
        
        <!-- MAIN VERIFICATION FORM -->
        <form method="POST" action="{{ route('verification.verify') }}" id="verifyForm">
            @csrf
            <input type="text" 
                   name="code" 
                   class="code-input" 
                   maxlength="6" 
                   placeholder="000000"
                   required
                   autofocus
                   autocomplete="off">
            <br>
            <button type="submit" class="btn">
                <i class="fas fa-check-circle" style="margin-right: 8px;"></i>
                Verify Email
            </button>
        </form>
        
        <p style="margin: 20px 0; color: #6b7280;">Didn't receive the code?</p>

        <!-- RESEND FORM -->
        <form method="POST" action="{{ route('verification.resend') }}">
            @csrf
            <button type="submit" class="btn btn-resend">
                <i class="fas fa-redo-alt" style="margin-right: 8px;"></i>
                Resend Verification Code
            </button>
        </form>
        
        <div class="links">
            <a href="{{ route('logout') }}" 
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                ↻ Use different account
            </a>
        </div>
        
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
    
    <script>
        // Wait for DOM to load
        document.addEventListener('DOMContentLoaded', function() {
            const codeInput = document.querySelector('.code-input');
            
            // Auto-focus the input
            if (codeInput) {
                codeInput.focus();
                
                // Only allow numbers - clean input
                codeInput.addEventListener('input', function(e) {
                    this.value = this.value.replace(/\D/g, '');
                });
                
                // Prevent non-numeric input
                codeInput.addEventListener('keypress', function(e) {
                    if (!/[0-9]/.test(e.key)) {
                        e.preventDefault();
                    }
                });
                
                // Optional: Show a hint when 6 digits are entered
                codeInput.addEventListener('input', function() {
                    if (this.value.length === 6) {
                        // Just visual feedback - don't auto-submit
                        this.style.borderColor = '#10b981';
                        this.style.boxShadow = '0 0 0 3px rgba(16, 185, 129, 0.1)';
                    } else {
                        this.style.borderColor = '#d1d5db';
                        this.style.boxShadow = 'none';
                    }
                });
            }
        });
    </script>
</body>
</html>