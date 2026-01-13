<!DOCTYPE html>
<html>

<head>
    <title>Email Verification Code</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div
        style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; text-align: center;">
        <h2 style="color: #696cff;">Email Verification Code</h2>
        <p>Your verification code for {{ config('app.name') }} is:</p>
        <h1
            style="font-size: 36px; letter-spacing: 5px; background: #f0f2f5; padding: 10px; border-radius: 5px; display: inline-block;">
            {{ $otp }}</h1>
        <p>This code is valid for 10 minutes.</p>
        <p>If you did not request this, please ignore this email.</p>
    </div>
</body>

</html>