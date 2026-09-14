<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="{{ vasset('css/darkmode.css') }}">
    <script src="{{ vasset('js/darkmode.js') }}"></script>
</head>
<body>
    <iframe height="945"  width="1905" src="/assets/{{$data->file}}"></iframe>
<script src="{{ vasset('assets/js/voice-input.js') }}"></script>
</body>
</html>