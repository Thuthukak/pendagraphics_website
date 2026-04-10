<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

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
        
        <!--
            Blade fallback for meta tags. 
            This guarantees Google always sees them even if SSR fails.
        -->
        <?php
        $seo = $page['props']['seo'] ?? [];
        ?>

        @if(!empty($seo))
            <title>{{ $seo['title'] ?? config('app.name') }}</title>
            <meta name="description" content="{{ $seo['description'] ?? '' }}">
            <meta name="keywords" content="{{ $seo['keywords'] ?? '' }}">
            <link rel="canonical" href="{{ $seo['canonical_url'] ?? url()->current() }}">
            <meta property="og:title" content="{{ $seo['og_title'] ?? '' }}">
            <meta property="og:description" content="{{ $seo['og_description'] ?? '' }}">
            <meta property="og:image" content="{{ $seo['og_image'] ?? '' }}">
            <meta property="og:url" content="{{ $seo['og_url'] ?? '' }}">
            <meta property="og:type" content="website">
            <meta name="twitter:card" content="{{ $seo['twitter_card'] ?? 'summary_large_image' }}">
            <meta name="twitter:title" content="{{ $seo['og_title'] ?? '' }}">
            <meta name="twitter:description" content="{{ $seo['og_description'] ?? '' }}">
            <meta name="twitter:image" content="{{ $seo['og_image'] ?? '' }}">
            <meta name="robots" content="index, follow">
        @endif
        
        @inertiaHead
        
    </head>
    <body class="font-sans antialiased">
        @inertia

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>