<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $body;

    /**
     * De ontvanger. Wordt meegegeven zodat de mail ook werkt buiten een web
     * request, waar geen ingelogde gebruiker is.
     */
    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct($subject = "Default Subject", $body = "Default email body", $user = null)
    {
        $this->subject = $subject;
        $this->body = $body;
        $this->user = $user;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject($this->subject)
                    ->view('emailstemplate')  // View for the email body
                    ->with([
                        'body' => $this->body,
                        'user' => $this->user,
                    ]);  // Data passed to the view
    }
}
