<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description"
        content="AgroBandung - Platform pemesanan tiket wisata agro terbaik di Bandung. Kunjungi kebun strawberry, teh, kopi arabika, dan taman bunga.">
    <meta name="keywords"
        content="wisata agro, bandung, kebun strawberry, kebun teh, kopi arabika, taman bunga, ciwidey, lembang">
    <meta name="author" content="AgroBandung">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="AgroBandung - Wisata Agro Terbaik di Bandung">
    <meta property="og:description"
        content="Temukan wisata agro terbaik di Bandung — dari kebun teh, strawberry, hingga kopi arabika.">
    <meta property="og:image" content="https://images.unsplash.com/photo-1582719508461-905c673771fd?w=1200">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="AgroBandung - Wisata Agro Terbaik di Bandung">
    <meta name="twitter:description"
        content="Temukan wisata agro terbaik di Bandung — dari kebun teh, strawberry, hingga kopi arabika.">

    <title>{{ get_setting('app_name', 'AgroBandung') }} - Wisata Agro Terbaik di Bandung</title>
    <link rel="icon" href="{{ get_setting('app_logo') ? asset('storage/' . get_setting('app_logo')) : asset('frontend/img/logo.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('frontend/css/style.css') }}" rel="stylesheet">
    @stack('styles')
</head>
