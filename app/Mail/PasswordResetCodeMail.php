<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The verification code or token.
     */
    public $code;
    public $token;
    public $isToken;

    /**
     * Create a new message instance.
     */
    public function __construct($codeOrToken, $isToken = false)
    {
        $this->isToken = $isToken;
        if ($isToken) {
            $this->token = $codeOrToken;
        } else {
            $this->code = $codeOrToken;
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->isToken ? 'Password Reset Link' : 'Password Reset Code';
        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $view = $this->isToken ? 'emails.PasswordResetToken' : 'emails.PasswordResetCode';
        $data = $this->isToken ? ['token' => $this->token] : ['code' => $this->code];
        
        return new Content(
            view: $view,
            with: $data,
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
