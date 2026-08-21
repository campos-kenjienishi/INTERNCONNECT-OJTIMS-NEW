<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Something Went Wrong</title>
    <link rel="stylesheet" href="{{ vasset('css/pages/error-page.css') }}">
</head>
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
