<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Mail\Mailable as MailableContract;

class PrintContentsEmail extends Mailable implements MailableContract
{
    use Queueable, SerializesModels;

    public $studentData;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($studentData)
    {
        $this->studentData = $studentData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.ojtreport')
                    ->with(['studentData' => $this->studentData])
                    ->subject('Student OJT Information Report');
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
