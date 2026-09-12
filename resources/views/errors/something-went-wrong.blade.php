<!DOCTYPE html>
<html lang="en">
<head>
    <script>
        (function() {
            try {
                var theme = localStorage.getItem('theme');
                if (!theme) {
                    var legacy = localStorage.getItem('darkMode') || localStorage.getItem('internconnect_darkmode');
                    if (legacy === 'true' || legacy === 'enabled' || legacy === '1') theme = 'dark';
                }
                if (theme === 'dark' || (!theme && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark-mode');
                    document.documentElement.setAttribute('data-theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark-mode');
                    document.documentElement.setAttribute('data-theme', 'light');
                }
            } catch (e) {}
        })();
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Something Went Wrong</title>
    <link rel="stylesheet" href="{{ vasset('css/pages/error-page.css') }}">
    <link rel="stylesheet" href="{{ vasset('css/darkmode.css') }}">
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
    <script src="{{ vasset('js/darkmode.js') }}"></script>
</body>
</html>
