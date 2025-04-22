<!DOCTYPE html>
<html>
<head>
    <title>Password Reset OTP</title>
    <style>
        .container {
            max-width: 600px;
            margin: 0 auto;
            font-family: Arial, sans-serif;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        .otp {
            font-size: 24px;
            font-weight: bold;
            color: #2d89ef;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Hello, {{ $data['name'] }}</h2>
        <p>You requested a password reset. Use the OTP below to reset your password:</p>
        <p class="otp">{{ $data['otp'] }}</p>
        <p>This OTP will expire in 10 minutes.</p>
        <p>If you did not request this, please ignore this email.</p>
        <div class="footer">
            <p>Thanks,<br>The {{ env('APP_NAME') }} Team</p>
        </div>
    </div>
</body>
</html>
