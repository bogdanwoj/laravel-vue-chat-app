<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- Meta tags, title, styles, etc. -->
</head>
<body class="antialiased">
<div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center py-4 sm:pt-0">
    <div class="hidden fixed top-0 right-0 px-6 py-4 sm:block">
        @auth
        @else
            @include('auth.login')
        @endauth
    </div>
</div>
</body>
</html>
