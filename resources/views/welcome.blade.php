<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#111827">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Crimpers POS">
    <title>The Crimpers</title>
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192.svg') }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <style>
        body { font-family:'Outfit',sans-serif; background:#f0fdf4; color:#1e293b; margin:0; }
    </style>
</head>
<body class="antialiased min-h-screen flex items-center justify-center">
    <div class="text-center px-4">
        <div class="w-20 h-20 rounded-3xl bg-green-500 flex items-center justify-center mx-auto mb-6 shadow-lg">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
            </svg>
        </div>
        <h1 class="text-4xl font-bold text-green-700 mb-2">The Crimpers</h1>
        <p class="text-slate-500 mb-8">Professional Salon POS System</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('login') }}"
               class="px-8 py-3 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-xl transition-all shadow-md hover:shadow-lg">
                Staff Login
            </a>
        </div>
    </div>
</body>
</html>
