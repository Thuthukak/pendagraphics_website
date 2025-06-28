<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- SEO Meta Tags -->
        <meta name="description" content="Professional web design, graphic design, and digital marketing services. Transform your brand with stunning designs and powerful websites.">
        <meta name="keywords" content="web design, graphic design, digital marketing, brand identity, e-commerce">
        <meta name="author" content="Your Company Name">
        
        <!-- Open Graph Meta Tags -->
        <meta property="og:title" content="Professional Design & Development Services">
        <meta property="og:description" content="Empowering your brand with stunning designs and websites">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        
        <!-- Twitter Card Meta Tags -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="Professional Design & Development Services">
        <meta name="twitter:description" content="Empowering your brand with stunning designs and websites">
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

        <!-- icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
    @inertia
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
