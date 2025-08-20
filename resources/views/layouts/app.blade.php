<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/png">

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        @include('layouts.navigation')

       <!-- Page Heading -->
    @if (isset($header))
        <header class="bg-gradient-to-r from-[#27aae1] to-[#262262] shadow text-white flex items-center px-6 py-4">
          <!-- Brand Logo + Title -->
            <a href="{{ route('dashboard') }}" class="flex items-center">
                <img src="{{ asset('public\images\lipasmart-logo.png') }}" alt="LipaSmart Logo" height="60" width="60">
                <h1 class="ms-3 text-xl font-semibold">LipaSmart Invoicing System</h1>
            </a>

            <!-- Dynamic Page Header Content -->
            <div class="ml-auto">
              {{ $header }}
            </div>
        </header>
    @endif


        <!-- Page Content -->
        <main class="py-4">
            {{-- Flash success message --}}
            @if (session('success'))
                <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-2 rounded mb-4 mx-auto w-fit">
                    {{ session('success') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</body>
</html>
