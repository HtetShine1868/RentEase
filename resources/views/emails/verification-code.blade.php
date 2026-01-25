<!DOCTYPE html>
<html>
<head>
    <title>RMS Verification Code</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { text-align: center; padding: 20px 0; background: #4f46e5; color: white; }
        .code-box { 
            text-align: center; 
            margin: 30px 0; 
            padding: 20px; 
            background: #f8f9fa; 
            border-radius: 10px;
            border: 2px dashed #4f46e5;
        }
        .verification-code { 
            font-size: 36px; 
            font-weight: bold; 
            letter-spacing: 5px; 
            color: #4f46e5;
            margin: 10px 0;
        }
        .instructions { margin: 20px 0; }
        .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>RMS Verification</h1>
        </div>
        
        <p>Hello <strong>{{ $name }}</strong>,</p>
        
        <p>Thank you for registering with RMS (Rent & Service Management System).</p>
        
        <div class="code-box">
            <p>Your verification code is:</p>
            <div class="verification-code">{{ $code }}</div>
            <p>Enter this code on the verification page to complete your registration.</p>
        </div>
        
        <div class="instructions">
            <p><strong>Instructions:</strong></p>
            <ul>
                <li>Enter the 6-digit code above on the verification page</li>
                <li>The code is valid for 10 minutes</li>
                <li>If you didn't request this code, please ignore this email</li>
            </ul>
        </div>
        
        <p>If you have any issues, please contact our support team.</p>
        
        <div class="footer">
            <p>Best regards,<br><strong>RMS Team</strong></p>
            <p>&copy; {{ date('Y') }} RMS System. All rights reserved.</p>
            <p><small>This is an automated message, please do not reply.</small></p>
        </div>
    </div>
</body>
</html>