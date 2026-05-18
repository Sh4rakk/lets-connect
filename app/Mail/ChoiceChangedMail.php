<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ChoiceChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $name,
        public array $changes,
        public array $currentBookings
    ) {}

    public function build()
    {
        $roundTexts = [];
        $newChoices = [];

        foreach ($this->changes as $round => $change) {
            $roundTexts[] = 'Ronde ' . $round;
            $newChoices[] = $change['new_workshop_name'];
        }

        return $this->subject('Wijziging in jouw workshopplanning Let\'s Connect')
                    ->view('emails.choice_changed_mail')
                    ->with([
                        'name' => $this->name,
                        'rounds' => implode(', ', $roundTexts),
                        'newChoice' => implode(', ', $newChoices),
                        'changes' => $this->changes,
                        'currentBookings' => $this->currentBookings,
                    ]);
    }
}



