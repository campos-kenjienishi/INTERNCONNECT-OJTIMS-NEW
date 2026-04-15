<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>@yield('title', 'OJTIMS')</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">
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
</head>
<body>
    @yield('content')
    <script src="https://cdn.jsdelivr.net/npm/sienna-accessibility@latest/dist/sienna-accessibility.umd.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
