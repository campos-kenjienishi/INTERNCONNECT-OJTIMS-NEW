<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>InternConnect Announcement</title></head>
<body style="margin:0;padding:24px;background:#f3f4f6;font-family:Arial,sans-serif;color:#1f2937;">
    <table role="presentation" style="width:100%;max-width:640px;margin:auto;border-collapse:collapse;background:#fff;">
        <tr><td style="padding:26px;background:#800000;color:#fff;font-size:22px;font-weight:bold;">InternConnect <span style="color:#ffd700;">Announcement</span></td></tr>
        <tr><td style="padding:26px;">
            <p style="font-size:12px;color:#6b7280;">From {{ $announcement['coordinator'] }} · OJT Coordinator</p>
            <h1 style="font-size:22px;line-height:1.4;">{{ $announcement['title'] }}</h1>
            <div style="white-space:pre-wrap;overflow-wrap:anywhere;line-height:1.8;">{{ $announcement['content'] }}</div>
            <p style="margin:28px 0;"><a href="{{ $dashboardUrl }}" style="display:inline-block;padding:14px 22px;background:#800000;color:#fff;text-decoration:none;border-radius:8px;">View in InternConnect</a></p>
            <p style="font-size:12px;color:#6b7280;line-height:1.7;">Sign in using your own account. This notification reflects the announcement when it was published; check the system for later updates.</p>
        </td></tr>
    </table>
</body>
</html>
