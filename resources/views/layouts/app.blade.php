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
        <title>@yield('title', 'OJTIMS')</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="{{ vasset('css/mobile.css') }}">
        <!-- Sienna Accessibility Widget Custom CSS Override: Maroon Theme -->
        <style>
            /* Sienna widget main button and highlights */
            .sienna-widget__button,
            .sienna-widget__button:focus,
            .sienna-widget__button:hover,
            .sienna-widget__button[aria-pressed="true"],
            .sienna-widget__button[aria-expanded="true"],
            .sienna-widget__panel,
            .sienna-widget__panel-header {
                background: #7b1f2f !important;
                border-color: #7b1f2f !important;
                color: #fff !important;
            }
            .sienna-widget__icon,
            .sienna-widget__icon svg {
                fill: #fff !important;
                color: #fff !important;
            }
            .sienna-widget__button svg {
                fill: #fff !important;
            }
            /* Accent for toggles and focus */
            .sienna-widget__toggle:checked + .sienna-widget__toggle-slider {
                background: #7b1f2f !important;
                border-color: #7b1f2f !important;
            }
            .sienna-widget__panel .sienna-widget__option:focus {
                outline-color: #7b1f2f !important;
            }
        </style>
        <link rel="stylesheet" href="{{ vasset('css/darkmode.css') }}">
</head>
<body>
    @yield('content')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ vasset('js/darkmode.js') }}"></script>
</body>
</html>
