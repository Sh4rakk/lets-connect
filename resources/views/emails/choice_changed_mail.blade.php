
<style>
  body { font-family: 'Plus Jakarta Sans', Figtree, ui-sans-serif, system-ui, sans-serif; line-height: 1.6; color: #374151; margin: 0; padding: 20px; background: linear-gradient(135deg, #f0f0f5 0%, #f3f4f6 100%); }
  .wrap { background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.07); max-width: 600px; margin: 0 auto; }
  .subject-bar { background: #343469; padding: 16px 30px; border-bottom: 3px solid #f58220; }
  .subject-label { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #f58220; font-weight: 700; margin-bottom: 6px; display: block; }
  .subject-value { color: #fff; font-weight: 600; font-size: 13px; }
  .head { background: #343469; padding: 40px 30px; text-align: center; color: #fff; }
  .head h1 { margin: 0; font-size: 26px; font-weight: 600; letter-spacing: -0.5px; color: #fff; }
  .head p { font-size: 13px; opacity: 0.9; margin: 8px 0 0; color: #fff; }
  .inner { padding: 36px 30px; }
  .success { background: linear-gradient(135deg, #f0fdf4, #dcfce7); border-left: 4px solid #22c55e; padding: 18px 22px; border-radius: 8px; margin: 24px 0; display: flex; align-items: center; gap: 14px; }
  .success-icon { font-size: 28px; }
  .success-title { font-size: 15px; font-weight: 700; color: #15803d; margin: 0; }
  .success-sub { font-size: 13px; color: #4b5563; margin: 4px 0 0; }
  table { width: 100%; border-collapse: collapse; margin: 24px 0; font-size: 14px; }
  thead tr { background: #343469; color: #fff; }
  thead th { padding: 10px 14px; text-align: left; font-size: 12px; font-weight: 600; letter-spacing: 0.5px; }
  tbody tr { border-bottom: 1px solid #e5e7eb; }
  tbody tr:last-child { border-bottom: none; }
  tbody td { padding: 12px 14px; vertical-align: top; color: #374151; }
  tbody tr.changed { background: #fffbeb; }
  tbody tr.changed td { color: #1f2937; }
  .badge-changed { background: #f58220; color: #fff; font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.5px; display: inline-block; margin-left: 6px; vertical-align: middle; }
  .note { background: #fffaf5; border: 1px solid #fed9b8; border-radius: 8px; padding: 14px 16px; margin: 24px 0; font-size: 13px; color: #c65b0d; display: flex; align-items: flex-start; gap: 10px; }
  .divider { height: 1px; background: #e5e7eb; margin: 28px 0; }
  .foot { background: #f9fafb; padding: 20px 30px; text-align: center; border-top: 1px solid #e5e7eb; font-size: 12px; color: #6b7280; }
  .brand { color: #f58220; font-weight: 600; }
  p { margin: 0 0 14px; font-size: 15px; color: #374151; }
</style>
<div>
  <div class="wrap">
    <div class="subject-bar">
      <div class="subject-label">Onderwerp</div>
      <div class="subject-value">Wijziging in jouw workshopplanning Let's Connect</div>
    </div>

    <div class="head">
      <h1>Let's Connect</h1>
      <p>Bevestiging wijziging planning</p>
    </div>

    <div class="inner">
      <p>Beste {{$name}},</p>
      <p>We hebben je workshopkeuze voor {{$rounds}} aangepast zoals je hebt gevraagd. Hieronder vind je je bijgewerkte planning voor Let's Connect:</p>

      <div class="success">
        <div class="success-icon">✅</div>
        <div>
          <div class="success-title">Wijziging doorgevoerd</div>
          <div class="success-sub">{{$rounds}} is gewijzigd naar <strong>{{$newChoice}}</strong></div>
        </div>
      </div>

      <p><strong>Je volledige planning voor Let's Connect:</strong></p>
      <table>
        <thead>
          <tr>
            <th>Ronde</th>
            <th>Workshop</th>
            <th>Lokaal</th>
            <th>Tijd</th>
          </tr>
        </thead>
        <tbody>
          @if(!empty($currentBookings))
            @for($i = 1; $i <= 3; $i++)
              @if(isset($currentBookings[$i]))
              <tr @if(isset($changes[$i]))class="changed"@endif>
                <td><strong>Ronde {{$i}}</strong></td>
                <td>{{ $currentBookings[$i]['workshop_name'] }}@if(isset($changes[$i])) <span class="badge-changed">gewijzigd</span>@endif</td>
                <td>{{ $currentBookings[$i]['workshop_location'] }}</td>
                <td>{{ $currentBookings[$i]['time'] }}</td>
              </tr>
              @else
              <tr>
                <td><strong>Ronde {{$i}}</strong></td>
                <td colspan="3" style="color: #999;">Geen workshop gekozen</td>
              </tr>
              @endif
            @endfor
          @else
          <tr>
            <td colspan="4" style="text-align: center; color: #999;">Geen boekingen beschikbaar</td>
          </tr>
          @endif
        </tbody>
      </table>

      <div class="note">
        <span style="font-size:18px;">📌</span>
        <span>Bewaar deze mail als bevestiging van je bijgewerkte planning. Vergeet je stempelkaart niet op de dag zelf!</span>
      </div>

      <p style="margin-bottom:0;">Heb je nog vragen? Neem dan contact op met je LOB'er.</p>

      <div class="divider"></div>

      <p style="font-size:13px;color:#6b7280;margin:0;">Met vriendelijke groet,<br><strong style="color:#2a2a4b;">Let's Connect – Deltion College</strong></p>
    </div>

    <div class="foot">
      <p style="margin:0;">© 2025 <span class="brand">Let's Connect</span> &nbsp;·&nbsp; All rights reserved</p>
    </div>
  </div>
</div>
