<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Requirement Document Denied</title>
</head>
<body style="margin:0; padding:0; background:#f5f5f5; font-family:Arial, sans-serif; color:#1a1a1a;">
    <div style="max-width:640px; margin:0 auto; padding:28px 16px;">
        <div style="background:#ffffff; border-radius:12px; overflow:hidden; border:1px solid #eeeeee;">
            <div style="background:#7f0000; color:#ffffff; padding:20px 24px;">
                <h1 style="margin:0; font-size:20px;">Requirement Document Denied</h1>
            </div>

            <div style="padding:24px;">
                <p style="font-size:15px; line-height:1.6; margin:0 0 14px;">
                    Your submitted requirement document was denied by your professor.
                </p>

                <p style="font-size:15px; line-height:1.6; margin:0 0 14px;">
                    <strong>Requirement:</strong> {{ $requirement->fileName }}
                </p>

                <div style="background:#fff5f5; border:1px solid #fecaca; border-radius:10px; padding:14px 16px; margin:18px 0;">
                    <p style="font-size:13px; color:#7f1d1d; font-weight:bold; margin:0 0 8px;">Reason for denial</p>
                    <p style="font-size:15px; line-height:1.6; margin:0; color:#1a1a1a;">{{ $reason }}</p>
                </div>

                <p style="font-size:15px; line-height:1.6; margin:0;">
                    Please review the reason, update your document if needed, and submit it again through InternConnect.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
