<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Bevestiging van je planning</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px #ddd;
            margin: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background:#343469;
            color: #fff;
        }
        .footer {
            margin-top: 20px;
            font-size: 14px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Beste {{ auth()->user()->name }},</h1>
    <p>
       Bedankt voor je aanmelding! 
       Leuk dat jij je hebt opgegeven voor netwerkevenement Let's Connect.
       Je wordt om 12:30 op het Sweelinckplein verwacht voor de centrale opening.
       Hier zijn de details van de workshops die jij hebt gekozen:
    </p>

    <table>
        <tr>
            <th>Datum</th>
            <th>Tijd</th>
            <th>Workshop</th>
            <th>Lokaal</th>
        </tr>
        <tr>
            <td>27-05-2025</td>
            <td>13:00 - 13:45</td>
            <td>
                @php
                    $results = 
                    DB::table('workshops AS ws')
                    ->join('workshop_moments AS wm', 'ws.id', '=', 'wm.workshop_id')
                    ->join('bookings AS bk', 'wm.id', '=', 'bk.wm_id')
                    ->select('ws.name')
                    ->where('bk.student_id', '=', auth()->user()->id)
                    ->where('wm.moment_id', '=', '1')
                    ->get()->toArray();

                    foreach ($results as $result) {
                        echo $result->name;
                    }
                @endphp
            </td>
            <td>
                @php
                    $results = 
                    DB::table('workshops AS ws')
                    ->join('workshop_moments AS wm', 'ws.id', '=', 'wm.workshop_id')
                    ->join('bookings AS bk', 'wm.id', '=', 'bk.wm_id')
                    ->select('ws.location')
                    ->where('bk.student_id', '=', auth()->user()->id)
                    ->where('wm.moment_id', '=', '1')
                    ->get()->toArray();

                    foreach ($results as $result) {
                        echo $result->location;
                    }
                @endphp
            </td>
        </tr>
        <tr>
            <td>27-05-2025</td>
            <td>13:45 - 14:30</td>
            <td>
                @php
                    $results = 
                    DB::table('workshops AS ws')
                    ->join('workshop_moments AS wm', 'ws.id', '=', 'wm.workshop_id')
                    ->join('bookings AS bk', 'wm.id', '=', 'bk.wm_id')
                    ->select('ws.name')
                    ->where('bk.student_id', '=', auth()->user()->id)
                    ->where('wm.moment_id', '=', '2')
                    ->get()->toArray();

                    foreach ($results as $result) {
                        echo $result->name;
                    }
                @endphp
            </td>
            <td>
                @php
                    $results = 
                    DB::table('workshops AS ws')
                    ->join('workshop_moments AS wm', 'ws.id', '=', 'wm.workshop_id')
                    ->join('bookings AS bk', 'wm.id', '=', 'bk.wm_id')
                    ->select('ws.location')
                    ->where('bk.student_id', '=', auth()->user()->id)
                    ->where('wm.moment_id', '=', '2')
                    ->get()->toArray();

                    foreach ($results as $result) {
                        echo $result->location;
                    }
                @endphp
            </td>
        </tr>
        <tr>
            <td>27-05-2025</td>
            <td>15:00 - 15:45</td>
            <td>
                @php
                    $results = 
                    DB::table('workshops AS ws')
                    ->join('workshop_moments AS wm', 'ws.id', '=', 'wm.workshop_id')
                    ->join('bookings AS bk', 'wm.id', '=', 'bk.wm_id')
                    ->select('ws.name')
                    ->where('bk.student_id', '=', auth()->user()->id)
                    ->where('wm.moment_id', '=', '3')
                    ->get()->toArray();

                    foreach ($results as $result) {
                        echo $result->name;
                    }
                @endphp
            </td>
            <td>
                @php
                    $results = 
                    DB::table('workshops AS ws')
                    ->join('workshop_moments AS wm', 'ws.id', '=', 'wm.workshop_id')
                    ->join('bookings AS bk', 'wm.id', '=', 'bk.wm_id')
                    ->select('ws.location')
                    ->where('bk.student_id', '=', auth()->user()->id)
                    ->where('wm.moment_id', '=', '3')
                    ->get()->toArray();

                    foreach ($results as $result) {
                        echo $result->location;
                    }
                @endphp
            </td>
        </tr>
    </table>

    <p>Je ontvangt deze e-mail ter bevestiging van je gekozen planning. Mocht je vragen hebben of wijzigingen willen aanbrengen, neem dan contact met je LOB-er.</p>

    <div class="footer">
        Met vriendelijke groet, <br>
        <strong>Lets Connect, </strong>
        <a href="www.deltion.nl">Deltion College</a>
    </div>
</div>

</body>
</html>
