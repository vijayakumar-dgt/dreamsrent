<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <title>Password Reset OTP</title>
</head>
<body>
    <div class="forgot-password container">
        <h2>Hello, {{ $data['name'] }}</h2>
        <p>You requested a password reset. Use the OTP below to reset your password:</p>
        <p class="otp">{{ $data['otp'] }}</p>
        <p>This OTP will expire in 10 minutes.</p>
        <p>If you did not request this, please ignore this email.</p>
        <div class="footer">
            <p>Thanks,<br>The {{ $companyName ?? 'Dreamsrent' }} Team</p>
        </div>
    </div>
</body>
</html>
