<?php

namespace App\Mail;


use Illuminate\Mail\Mailable;

class LoginCode extends Mailable
{
    public function __construct(public string $code) {}

    public function build(): static
    {
        return $this
            ->from(
                config('mail.from.address'),
                config('mail.from.name')
            )
            ->subject('Your sign-in code')
            ->view('login_code')
            ->with(['code' => $this->code]);
    }
}
