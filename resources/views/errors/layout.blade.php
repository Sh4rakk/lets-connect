<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Geen Toegang</title>
    <link rel="stylesheet" href="{{ asset('/css/error.css') }}">
    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
        }

        .background-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url("{{ asset('/images/24213_SAVETHEDATE_LETS_CONNECT_01.jpg') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            z-index: -1;
        }

        .message-box {
            background-color: rgba(1, 1, 116, 0.9);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            z-index: 10;
            max-width: 80%;
            font-size: 1.5rem;
        }

        .countdown {
            font-weight: bold;
            font-size: 2rem;
        }

        @media (max-width: 768px) {
            .message-box {
                font-size: 1.2rem;
            }

            .countdown {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 480px) {
            .message-box {
                font-size: 1rem;
                max-width: 95%;
            }
        }
    </style>
</head>
<body>
    <div class="message-box">
        <h1>Geen Toegang</h1>
        <p>Je hebt geen toegang tot deze pagina.</p>
        <div id="timer" class="countdown">5</div>
    </div>

    <div class="background-container"></div>

    <script>
        let countdown = 5;
        const timerElement = document.getElementById('timer');

        if (timerElement) {
            const interval = setInterval(() => {
                countdown--;
                timerElement.textContent = countdown;

                if (countdown === 0) {
                    clearInterval(interval);
                    setTimeout(() => {
                        window.location.href = "{{ url('/dashboard') }}";
                    }, 1000);
                }
            }, 1000);
        }
    </script>
</body>
</html>
