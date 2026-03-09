<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Geen Toegang</title>
    <style>
       .body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 0;
            padding: 0;
            background: linear-gradient(to bottom, #9B5F1B, #D3873B);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            position: relative;
            color: white;
            overflow: hidden;
        }

        .background-container {
            position: absolute;
            bottom: 0%;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            z-index: 0;
        }


        .background-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            box-shadow: 0 0 60px 30px rgba(0, 0, 0, 0.9);
            z-index: -1;
        }

        .message-box {
            background-color: #010174;
            color: white;
            padding: 40px;
            border-radius: 10px;
            border: 1px solid #010174;
            max-width: 45%;
            margin: 20px auto;
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .message-box pre {
            font-size: 20px;
            font-weight: bold;
            margin: 10px 0;
        }

        .countdown {
            display: flex;
            justify-content: center;
            font-size: 16px;
        }

        #timer {
            font-weight: bold;
        }

        .illustration {
            position: absolute;
            bottom: 0;
            width: 100%;
            max-width: 700px;
            opacity: 0.9;
        }
    </style>
</head>
<body>

    <div class="message-box">
        <pre>Je hebt geen toegang tot deze pagina</pre>
        <div class="countdown">
            <pre>Je wordt over&nbsp;</pre>
            <pre id="timer">5</pre>
            <pre>&nbsp;seconden terug gestuurd naar de home page</pre>
        </div>
    </div>


    <img class="background-container" src="{{ asset('/images/24213_SAVETHEDATE_LETS_CONNECT_01.jpg') }}">

    <script>
        let countdown = 5;
        const timerElement = document.getElementById('timer');

        const interval = setInterval(() => {
            countdown--;
            timerElement.textContent = countdown;

            if (countdown === 0) {
                clearInterval(interval);

                setTimeout(() => {
                    window.location.href = '{{ route('dashboard') }}';
                }, 1000);
            }
        }, 1000);
    </script>

</body>
</html>
