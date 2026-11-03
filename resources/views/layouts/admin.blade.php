 filepath: resources/views/layouts/admin.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Admin')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSS utama admin -->
    <link rel="stylesheet" href="{{ asset('css/chatadmin.css') }}">
    <!-- Tambahkan CSS lain jika perlu -->
    @stack('styles')
</head>
<body style="background:#f6f8fa; min-height:100vh;">
    {{-- Sidebar --}}
    @if(View::exists('partials.sidebar'))
        @include('partials.sidebar')
    @endif

    {{-- Main Content --}}
    <main style="margin-left:240px; min-height:100vh; padding:24px;">
        @yield('content')
    </main>

    {{-- Script tambahan --}}
    @stack('scripts')
</body>
</html>