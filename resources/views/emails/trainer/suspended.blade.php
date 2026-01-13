<!DOCTYPE html>
<html>

<head>
    <title>Account Suspended</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
        <h2 style="color: #F44336;">Account Suspended</h2>
        <p>Dear {{ $trainer->name }},</p>
        <p>This email is to inform you that your trainer account on <strong>{{ config('app.name') }}</strong> has been
            suspended by the administrator.</p>
        <p>If you believe this is a mistake or have any questions regarding this action, please contact our support team
            immediately.</p>
        <p>Best Regards,<br>{{ config('app.name') }} Team</p>
    </div>
</body>

</html>