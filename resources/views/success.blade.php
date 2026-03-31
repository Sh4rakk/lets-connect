<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Succes - Let's Connect</title>
    <link href="{{ asset('/css/success.css') }}" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@700&display=swap" rel="stylesheet">
</head>
<body>
@php
    $successTitle = session('title') ?? 'Bedankt voor je aanmelding';
    $successMessage = session('message') ?? session('status') ?? 'Je inschrijvingen zijn succesvol opgeslagen. Je ontvangt zo snel mogelijk een mail ter bevestiging van je gekozen inschrijvingen. Je kan dit scherm nu sluiten.';
@endphp

<div class="success-page">
    <div class="success-card">
        <div class="success-brand">
            <img src="{{ asset('/images/deltion.png') }}" alt="Logo">
            <span class="success-brand-sep"></span>
            <div class="success-brand-text">
                <span class="deltion-blue">Let's</span>
                <span class="deltion-orange">Connect</span>
            </div>
        </div>

        <h1 class="deltion-orange success-title">{{ $successTitle }}</h1>
        <p class="deltion-blue success-subtitle">{{ $successMessage }}</p>

        <a href="{{ url('/dashboard') }}" class="success-action">Terug naar dashboard</a>
    </div>
</div>
</body>
</html>
