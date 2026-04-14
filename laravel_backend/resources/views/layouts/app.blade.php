{{--
    @package La Bottega — Layout
    @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
    @version 1.0.0 (FlorenceEGI — La Bottega)
    @date 2026-04-12
    @purpose Layout Blade principale — host per React 19 SPA
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'La Bottega') }} — FlorenceEGI</title>
    <meta name="description" content="{{ __('bottega.meta_description') }}">
    <link rel="canonical" href="https://la-bottega.florenceegi.com">

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ config('app.name', 'La Bottega') }} — FlorenceEGI">
    <meta property="og:description" content="{{ __('bottega.meta_description') }}">
    <meta property="og:url" content="https://la-bottega.florenceegi.com">
    <meta property="og:site_name" content="FlorenceEGI">

    {{-- Twitter --}}
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ config('app.name', 'La Bottega') }} — FlorenceEGI">
    <meta name="twitter:description" content="{{ __('bottega.meta_description') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.tsx'])
</head>
<body class="antialiased">
    <div id="bottega-root"></div>
</body>
</html>
