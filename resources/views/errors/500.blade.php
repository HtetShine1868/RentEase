<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Server Error</title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
        }
        .error-container {
            background: white;
            padding: 3rem;
            border-radius: 1rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 500px;
            width: 90%;
            text-align: center;
        }
        h1 {
            font-size: 6rem;
            margin: 0;
            color: #f56565;
            line-height: 1;
        }
        h2 {
            font-size: 2rem;
            margin: 0 0 1rem;
            color: #2d3748;
        }
        p {
            color: #718096;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        .btn {
            display: inline-block;
            background: #4299e1;
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 0.5rem;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #3182ce;
        }
        .error-details {
            background: #f7fafc;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-top: 2rem;
            text-align: left;
        }
        .error-details pre {
            margin: 0;
            font-size: 0.8rem;
            color: #4a5568;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1>500</h1>
        <h2>Server Error</h2>
        <p>Oops! Something went wrong on our end. We're working to fix it.</p>
        
        @if(isset($error) && config('app.debug'))
            <div class="error-details">
                <strong>Error Details:</strong>
                <pre>{{ $error }}</pre>
            </div>
        @endif
        
        <a href="{{ url('/') }}" class="btn">Go to Homepage</a>
    </div>
</body>
</html>