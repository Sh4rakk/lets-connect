<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\BookingConfirmationMailer;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ResendBookingConfirmations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:resend-confirmations
                            {--since= : Alleen boekingen aangepast op of na deze UTC tijd, bv. "2026-11-09 00:00"}
                            {--all : Alle studenten zonder bevestiging, hoe oud ook}
                            {--limit= : Alleen de eerste N studenten, om de mailserver eerst te testen}
                            {--send : Echt versturen; zonder deze optie wordt alleen een lijst getoond}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Stuur de bevestigingsmail naar studenten die er nooit een hebben gekregen';

    /**
     * Execute the console command.
     */
    public function handle(BookingConfirmationMailer $mailer): int
    {
        $since = $this->option('since');

        if (!$since && !$this->option('all')) {
            $this->error('Geef --since="YYYY-MM-DD HH:MM" (UTC) mee, of --all voor alle studenten zonder bevestiging.');
            $this->line('Boekingen van voor de bevestigingstracking zijn ook onbevestigd, maar die studenten kregen al een mail van de oude code.');

            return self::FAILURE;
        }

        try {
            $cutoff = $since ? Carbon::parse($since) : null;
        } catch (\Exception) {
            $this->error("Kan --since=\"{$since}\" niet als datum lezen.");

            return self::FAILURE;
        }

        $students = User::whereNull('confirmation_sent_at')
            ->whereHas('bookings', fn ($query) => $query->when($cutoff, fn ($query) => $query->where('updated_at', '>=', $cutoff)))
            ->withMax('bookings', 'updated_at')
            ->orderBy('bookings_max_updated_at')
            ->when($this->option('limit'), fn ($query) => $query->limit((int) $this->option('limit')))
            ->get();

        if ($students->isEmpty()) {
            $this->info('Er wachten geen studenten op een bevestigingsmail.');

            return self::SUCCESS;
        }

        $this->table(
            ['Student', 'E-mail', 'Laatst opgeslagen (UTC)'],
            $students->map(fn (User $student) => [
                $student->name,
                $student->email,
                $student->bookings_max_updated_at,
            ])
        );

        if (!$this->option('send')) {
            $this->warn($students->count() . ' student(en) krijgen elk 1 mail. Draai opnieuw met --send om te versturen.');

            return self::SUCCESS;
        }

        $sent = 0;
        $failed = 0;

        foreach ($students as $student) {
            if ($mailer->send($student)) {
                $sent++;
                continue;
            }

            $failed++;
            $this->error("Kon niet versturen naar {$student->email}, zie de log.");
        }

        $this->info("{$sent} bevestigingsmail(s) verstuurd, {$failed} mislukt.");
        $this->line('Mislukte blijven onbevestigd, dus dit command kan veilig opnieuw gedraaid worden.');

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }
}
