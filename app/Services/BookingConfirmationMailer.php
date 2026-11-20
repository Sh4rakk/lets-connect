<?php

namespace App\Services;

use App\Mail\SendMail;
use App\Models\Bookings;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class BookingConfirmationMailer
{
    private const SUBJECT = "Let's Connect";

    private const BODY = 'Bedankt voor je aanmelding voor Let\'s Connect. Hieronder vind je een overzicht van jouw workshops.';

    /**
     * Stuurt de bevestigingsmail, maar maximaal 1 keer per set gekozen workshops.
     *
     * Dezelfde keuzes nog een keer opslaan geeft geen tweede mail, een andere
     * keuze wel.
     *
     * @return bool true als er echt een mail is verstuurd
     */
    public function send(User $user): bool
    {
        $key = $this->confirmationKey($user);

        if ($key === '' || !$this->claim($user, $key)) {
            Log::info('Bevestigingsmail overgeslagen, deze keuzes zijn al bevestigd', [
                'student_id' => $user->id,
                'key' => $key,
            ]);

            return false;
        }

        try {
            Mail::to($user->email)->send(new SendMail(self::SUBJECT, self::BODY, $user));

            Log::info('Bevestigingsmail verstuurd', [
                'student_id' => $user->id,
                'key' => $key,
            ]);

            return true;
        } catch (Throwable $e) {
            // er is niks verstuurd, dus de claim weer vrijgeven zodat het later nog kan
            $this->releaseClaim($user);
            report($e);

            return false;
        }
    }

    /**
     * De gekozen workshopmomenten van de student, op volgorde, als 1 string.
     */
    private function confirmationKey(User $user): string
    {
        return Bookings::where('student_id', $user->id)
            ->orderBy('wm_id')
            ->pluck('wm_id')
            ->implode(',');
    }

    /**
     * Zet de bevestiging op de student, maar alleen als deze keuzes nog niet
     * bevestigd waren. De update met where is atomisch, dus twee requests
     * tegelijk kunnen nooit allebei een mail sturen.
     */
    private function claim(User $user, string $key): bool
    {
        $claimed = User::where('id', $user->id)
            ->where(function ($query) use ($key) {
                $query->whereNull('confirmation_sent_at')
                    ->orWhereNull('confirmation_key')
                    ->orWhere('confirmation_key', '!=', $key);
            })
            ->update([
                'confirmation_sent_at' => now(),
                'confirmation_key' => $key,
            ]);

        if ($claimed === 0) {
            return false;
        }

        $user->confirmation_sent_at = now();
        $user->confirmation_key = $key;

        return true;
    }

    private function releaseClaim(User $user): void
    {
        User::where('id', $user->id)->update([
            'confirmation_sent_at' => null,
            'confirmation_key' => null,
        ]);

        $user->confirmation_sent_at = null;
        $user->confirmation_key = null;
    }
}
