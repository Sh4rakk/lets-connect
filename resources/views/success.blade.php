<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Succes - Let's Connect</title>
    <link href="{{ asset('/css/dashboard.css') }}" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
</head>
<body>
@php
    $successTitle = session('title') ?? 'Bedankt voor je aanmelding';
    $successMessage = session('message') ?? session('status') ?? 'Je planning is succesvol opgeslagen. Je ontvangt zo snel mogelijk een mail ter bevestiging van je gekozen planning. Je kan dit scherm nu sluiten.';
@endphp

<div class="success-page">
    <div class="success-card">
        <img
            src="https://xerte.deltion.nl/USER-FILES/8708-abvries-site/media/letsconnect2.jpg"
            class="success-img"
            alt="Let's Connect"
        >

        <div class="success-brand">
            <span class="deltion-blue">Del</span><span class="deltion-orange">tion</span>
            <span class="success-brand-sep"></span>
            <span class="deltion-blue">Let's</span>
            <span class="deltion-orange">Connect</span>
        </div>

        <h1 class="deltion-orange success-title">{{ $successTitle }}</h1>
        <p class="deltion-blue success-subtitle">{{ $successMessage }}</p>

        <a href="{{ url('/dashboard') }}" class="success-action">Terug naar dashboard</a>
    </div>
</div>
</body>
</html>
