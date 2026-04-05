<?php

namespace App\Mail;

use App\Models\OjtEvaluationRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SupervisorEvaluationInvite extends Mailable
{
    use Queueable, SerializesModels;

    public $requestRow;

    public function __construct(OjtEvaluationRequest $requestRow)
    {
        $this->requestRow = $requestRow;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'OJT Evaluation Form Request',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.supervisor_evaluation_invite',
            with: [
                'requestRow' => $this->requestRow,
                'evaluationLink' => route('evaluation.form.show', ['token' => $this->requestRow->token]),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
