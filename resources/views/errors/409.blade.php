<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workshop Vol - Let's Connect</title>
    <link href="{{ asset('/css/dashboard.css') }}" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
</head>
<body>

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

        <h1 class="deltion-orange success-title">Helaas!</h1>
        <p class="deltion-blue success-subtitle">Deze workshop zit al vol. Wil je een nieuwe keuze maken?</p>

        <a href="{{ url('/dashboard') }}" class="success-action">Terug naar dashboard</a>
    </div>
</div>

</body>
</html>

