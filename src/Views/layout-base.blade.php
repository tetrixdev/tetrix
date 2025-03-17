<!DOCTYPE html>
<html lang="@yield('tx-lang', str_replace('_', '-', app()->getLocale()))">
<head>
    @section('tx-meta')
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    @show

    <title>@yield('tx-title', config('app.name'))</title>

    <!-- Fonts -->
    @section('tx-fonts')
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @show

    <!-- Styles / Scripts -->
    @section('tx-scripts')
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            @php trigger_error('Tetrix is currently expecting a vite setup, you can extend the lay-out and include a `scripts` section if needed') @endphp
        @endif
    @show
</head>
@section('tx-body')
    <body class="bg-tx-bg-body dark:bg-tx-bg-body-dark text-tx-txt dark:text-tx-txt-dark min-h-screen flex flex-col items-center">
    @yield('tx-header')
    @yield('tx-main')
    @yield('tx-footer')
    </body>
@show
</html>