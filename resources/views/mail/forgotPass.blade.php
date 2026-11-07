<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Reset your local password</title></head>
<body style="margin:0;padding:24px;background:#f6f3f3;font-family:Arial,sans-serif;color:#253047;">
    <div style="max-width:580px;margin:auto;background:#fff;border-radius:16px;overflow:hidden;">
        <div style="background:#650d0d;border-bottom:4px solid #ffd200;padding:24px;color:#fff;font-size:23px;font-weight:bold;">Intern<span style="color:#ffd200;">Connect</span></div>
        <div style="padding:28px;line-height:1.7;">
            <h1 style="font-size:21px;color:#650d0d;">Reset your local password</h1>
            <p>We received a request to reset the local password for your InternConnect account.</p>
            <p>This link expires in <strong>{{ $expiresInMinutes }} minutes</strong> and can be used only once. Requesting a new link invalidates the previous link.</p>
            <p style="margin:26px 0;"><a href="{{ $resetUrl }}" style="display:inline-block;background:#781616;color:#fff;text-decoration:none;padding:12px 22px;border-radius:8px;font-weight:bold;">Reset local password</a></p>
            <p>This changes only your InternConnect local password. Your institutional IdP password is managed separately by the Identity Provider.</p>
            <p>If you did not request this, do not click the link. You can ignore this email; your password has not been changed.</p>
            <p style="font-size:12px;color:#657087;">Do not forward this email or share the reset link.</p>
        </div>
    </div>
</body>
</html>
