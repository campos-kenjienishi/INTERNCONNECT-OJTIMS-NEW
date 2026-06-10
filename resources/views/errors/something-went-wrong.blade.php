<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Something Went Wrong</title>
    <style>
        :root {
            color-scheme: light;
            --bg: #f6f7fb;
            --card: #ffffff;
            --text: #172033;
            --muted: #5f6b85;
            --accent: #0f766e;
            --border: #d9dfeb;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background:
                radial-gradient(circle at top, #d8efe9 0%, transparent 38%),
                linear-gradient(180deg, var(--bg) 0%, #eef2f9 100%);
            color: var(--text);
        }

        .error-card {
            width: min(100%, 560px);
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 32px 28px;
            box-shadow: 0 18px 45px rgba(23, 32, 51, 0.12);
            text-align: center;
        }

        .badge {
            display: inline-block;
            margin-bottom: 16px;
            padding: 8px 14px;
            border-radius: 999px;
            background: #e6fffa;
            color: var(--accent);
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        h1 {
            margin: 0 0 12px;
            font-size: clamp(28px, 5vw, 38px);
            line-height: 1.1;
        }

        p {
            margin: 0 auto;
            max-width: 42ch;
            color: var(--muted);
            font-size: 16px;
            line-height: 1.6;
        }

        .actions {
            margin-top: 24px;
        }

        .button {
            display: inline-block;
            padding: 12px 20px;
            border-radius: 12px;
            background: var(--accent);
            color: #ffffff;
            text-decoration: none;
            font-weight: 600;
        }

        .status {
            margin-top: 18px;
            color: var(--muted);
            font-size: 14px;
        }
    </style>
</head>
<body>
    <main class="error-card">
        <div class="badge">System Notice</div>
        <h1>Something went wrong.</h1>
        <p>
            We could not complete your request right now. Please go back and try again in a moment.
            If this keeps happening, contact the system administrator.
        </p>
        <div class="actions">
            <a class="button" href="{{ url()->previous() }}">Go Back</a>
        </div>
        <div class="status">Error code: {{ $statusCode ?? 500 }}</div>
    </main>
</body>
</html>
