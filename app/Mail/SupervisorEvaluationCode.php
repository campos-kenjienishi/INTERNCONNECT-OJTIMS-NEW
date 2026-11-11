<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class SupervisorEvaluationCode extends Mailable
{
    public function __construct(public readonly string $code)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your InternConnect Evaluation Verification Code');
    }

    public function content(): Content
    {
        return new Content(view: 'mail.supervisor_evaluation_code');
    }
}
