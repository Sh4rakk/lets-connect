<?php

namespace App\Mail;

class TwoFactorCodeMail extends SendMail
{
    protected $verificationCode;
    
    /**
     * Create a new message instance.
     */
    public function __construct($code, $userName = '')
    {
        $this->verificationCode = $code;
        
        $subject = 'Your Two-Factor Authentication Code';
        $body = '<h2>Two-Factor Authentication</h2>
                <p>Hello ' . $userName . ',</p>
                <p>You are receiving this email because we received a login attempt for your account.</p>
                <p>Your two-factor authentication code is:</p>';
        
        parent::__construct($subject, $body);
    }
    
    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject($this->subject)
                    ->view('two-factor-email')  // A specialized view for 2FA emails
                    ->with([
                        'body' => $this->body,
                        'code' => $this->verificationCode
                    ]);
    }
}