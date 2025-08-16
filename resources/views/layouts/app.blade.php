<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Basic Meta Tags -->
        <title inertia>{{ $page['props']['seo']['title'] ?? config('app.name', 'Penda Graphics') }}</title>
        <meta name="description" content="{{ $page['props']['seo']['description'] ?? 'Professional web design, branding, and digital marketing services in South Africa.' }}">
        <meta name="keywords" content="{{ $page['props']['seo']['keywords'] ?? 'web design, branding, digital marketing, South Africa' }}">
        <meta name="robots" content="index, follow">
        
        <!-- Canonical URL -->
        <link rel="canonical" href="{{ $page['props']['seo']['canonical_url'] ?? url()->current() }}">
        
        <!-- Open Graph Meta Tags -->
        <meta property="og:title" content="{{ $page['props']['seo']['og_title'] ?? $page['props']['seo']['title'] ?? config('app.name') }}">
        <meta property="og:description" content="{{ $page['props']['seo']['og_description'] ?? $page['props']['seo']['description'] ?? 'Professional web design, branding, and digital marketing services in South Africa.' }}">
        <meta property="og:image" content="{{ $page['props']['seo']['og_image'] ?? asset('assets/images/penda_logo2.png') }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:image:alt" content="Penda Graphics Logo - Professional Web Design & Branding">
        <meta property="og:url" content="{{ $page['props']['seo']['og_url'] ?? url()->current() }}">
        <meta property="og:type" content="{{ $page['props']['seo']['og_type'] ?? 'website' }}">
        <meta property="og:site_name" content="{{ $page['props']['seo']['og_site_name'] ?? config('app.name') }}">
        
        <!-- Twitter Card Meta Tags -->
        <meta name="twitter:card" content="{{ $page['props']['seo']['twitter_card'] ?? 'summary_large_image' }}">
        <meta name="twitter:title" content="{{ $page['props']['seo']['twitter_title'] ?? $page['props']['seo']['og_title'] ?? $page['props']['seo']['title'] ?? config('app.name') }}">
        <meta name="twitter:description" content="{{ $page['props']['seo']['twitter_description'] ?? $page['props']['seo']['og_description'] ?? $page['props']['seo']['description'] ?? 'Professional web design, branding, and digital marketing services in South Africa.' }}">
        <meta name="twitter:image" content="{{ $page['props']['seo']['twitter_image'] ?? $page['props']['seo']['og_image'] ?? asset('assets/images/penda_logo2.png') }}">
        <meta name="twitter:image:alt" content="Penda Graphics Logo - Professional Web Design & Branding">
        
        <!-- Additional SEO Meta Tags -->
        <meta name="author" content="Penda Graphics">
        <meta name="theme-color" content="#005e91">
        
        <!-- Favicon -->
        <link rel="icon" href="{{ asset('favicon.ico') }}">
        <link rel="apple-touch-icon" href="{{ asset('assets/images/penda_logo2.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

        <!-- Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-Avb2QiuDEEvB4bZJYdft2mNjVShBftLdPG8FJ0V7irTLQ8Uo0qcPxh4Plq7G5tGm0rU+1SPhVotteLpBERwTkw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        
        <!-- Structured Data -->
        @if(isset($page['props']['structuredData']))
            <script type="application/ld+json">
                {!! $page['props']['structuredData'] !!}
            </script>
        @endif
    </body>
</html>