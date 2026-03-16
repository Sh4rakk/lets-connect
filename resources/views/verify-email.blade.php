<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $subject }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .code-container {
            margin: 25px 0;
            padding: 15px;
            background-color: #f3f4f6;
            border-radius: 5px;
            text-align: center;
        }
        .verification-code {
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 4px;
            color: #000;
        }
        .expiry-note {
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #888;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    {!! $body !!}
    
    @if(isset($verification_code))
    <div class="code-container">
        <div class="verification-code">{{ $verification_code }}</div>
    </div>
    <div class="expiry-note">
        This code will expire in 5 minutes. If you did not request this code, please ignore this email.
    </div>
    @endif
    
    <div class="footer">
        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </div>
</body>
</html>