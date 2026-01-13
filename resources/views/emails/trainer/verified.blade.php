<!DOCTYPE html>
<html>

<head>
    <title>Account Verified</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
        <h2 style="color: #4CAF50;">Congratulations! Your Account is Verified.</h2>
        <p>Dear {{ $trainer->name }},</p>
        <p>We are pleased to inform you that your account on <strong>{{ config('app.name') }}</strong> has been
            successfully verified.</p>
        <p>You can now access all features available to verified trainers, including managing programs and sessions.</p>
        <p>
            <a href="{{ route('trainer.login') }}"
                style="background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Login
                to Dashboard</a>
        </p>
        <p>If you have any questions, feel free to contact our support team.</p>
        <p>Best Regards,<br>{{ config('app.name') }} Team</p>
    </div>
</body>

</html>