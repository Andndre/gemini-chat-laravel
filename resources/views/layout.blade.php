<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Gemini Chat')</title>
    @vite('resources/sass/app.scss')
</head>
<body>
    @yield('content')
    @vite('resources/js/app.js')
</body>
</html>
