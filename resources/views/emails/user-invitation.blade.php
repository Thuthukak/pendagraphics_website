<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #212121; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .button { display: inline-block; padding: 12px 24px; background-color: #4682b4; color: white; text-decoration: none; border-radius: 6px; margin: 20px 0; }
        .footer { margin-top: 30px; font-size: 12px; color: #4682b4; }
    </style>
</head>
<body>
    <div class="container">
        <h1>You've Been Invited!</h1>
        
        <p>Hello,</p>
        
        <p>You have been invited to join <strong>{{ config('app.name') }}</strong>. Click the button below to accept your invitation and complete your registration.</p>
        
        <a href="{{ $acceptUrl }}" class="button">Accept Invitation</a>
        
        <p>Or copy and paste this URL into your browser:</p>
        <p style="word-break: break-all; color: #4682b4;">{{ $acceptUrl }}</p>
        
        <p><strong>Note:</strong> This invitation will expire {{ $expiresAt }}.</p>
        
        <div class="footer">
            <p>If you weren't expecting this invitation, you can safely ignore this email.</p>
        </div>
    </div>
</body>
</html>