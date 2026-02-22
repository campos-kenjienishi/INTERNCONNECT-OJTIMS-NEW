<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MOAReportEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $companies;
    public $students;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($companies, $students)
    {
        $this->companies = $companies;
        $this->students = $students;
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.ExpiredMOA')
        ->with(['companies' => $this->companies,
        'students' => $this->students,])
                    ->subject('Expired Memorandum of Agreements Report');
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
