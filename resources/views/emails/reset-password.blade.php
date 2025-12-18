<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Password Reset</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f6f8; font-family:Arial, Helvetica, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center" style="padding:40px 15px;">
                <table width="100%" max-width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.08);">
                    
                    <!-- Header -->
                    <tr>
                        <td align="center" style="padding:30px 20px; border-bottom:1px solid #eee;">
                            <h1 style="margin:0; font-size:24px; color:#333;">
                                {{ config('app.name') }}
                            </h1>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding:30px 25px; color:#555; font-size:15px; line-height:1.6;">
                            <p style="margin-top:0;">Hello,</p>

                            <p>
                                We received a request to reset your password for your
                                <strong>{{ config('app.name') }}</strong> account.
                            </p>

                            <p style="text-align:center; margin:30px 0;">
                                <a href="{{ $link }}"
                                   style="background:#4f46e5; color:#ffffff; text-decoration:none; padding:14px 28px; border-radius:6px; font-weight:bold; display:inline-block;">
                                    Reset Password
                                </a>
                            </p>

                            <p>
                                This password reset link will expire in a limited time.
                                If you did not request a password reset, no further action is required.
                            </p>

                            <p style="margin-bottom:0;">
                                Regards,<br>
                                <strong>{{ config('app.name') }} Team</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="padding:20px; background:#fafafa; border-top:1px solid #eee; font-size:12px; color:#999;">
                            © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
