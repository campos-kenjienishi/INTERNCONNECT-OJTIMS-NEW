<?php

namespace App\Mail;

use App\Models\FileRequirement;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RequirementDenied extends Mailable
{
    use Queueable, SerializesModels;

    public FileRequirement $requirement;
    public string $reason;

    public function __construct(FileRequirement $requirement, string $reason)
    {
        $this->requirement = $requirement;
        $this->reason = $reason;
    }

    public function build()
    {
        return $this->subject('Requirement Document Denied')
            ->view('mail.requirement_denied');
    }
}
