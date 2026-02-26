<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Request</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #f4f4f4; padding: 20px; border-radius: 10px;">
        <h2 style="color: #2563eb;">Password Reset Request</h2>

        <p>Hello {{ $userName }},</p>

        <p>You are receiving this email because we received a password reset request for your account with the role: <strong>{{ ucfirst($role) }}</strong>.</p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $resetUrl }}"
               style="display: inline-block; padding: 12px 24px; background-color: #2563eb; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;">
                Reset Password
            </a>
        </div>

        <p>This password reset link will expire in 24 hours.</p>

        <p>If you did not request a password reset, no further action is required.</p>

        <hr style="border: none; border-top: 1px solid #ddd; margin: 20px 0;">

        <p style="font-size: 12px; color: #666;">
            If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:<br>
            <a href="{{ $resetUrl }}" style="color: #2563eb; word-break: break-all;">{{ $resetUrl }}</a>
        </p>
    </div>
</body>
</html>
