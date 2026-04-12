<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OJT Evaluation Request</title>
</head>
<body style="font-family: Arial, sans-serif; color: #111; line-height: 1.6;">
    <h2 style="margin-bottom: 8px;">OJT Student Evaluation Request</h2>
    <p>You have been requested to evaluate an OJT student.</p>

    <p>
        <strong>Student:</strong> {{ $requestRow->student_name ?: $requestRow->student_num }}<br>
        <strong>Student Number:</strong> {{ $requestRow->student_num }}
    </p>

    <p>
        Please click the secure link below to open the evaluation form:
    </p>

    <p>
        <a href="{{ $evaluationLink }}" style="display:inline-block;padding:10px 14px;background:#b91c1c;color:#fff;text-decoration:none;border-radius:6px;">
            Open Evaluation Form
        </a>
    </p>

    <p style="font-size: 13px; color: #555;">
        This link may expire after some time. If it no longer works, please contact the student or school coordinator.
    </p>
</body>
</html>
