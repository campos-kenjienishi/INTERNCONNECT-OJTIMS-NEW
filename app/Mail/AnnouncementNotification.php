<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class AnnouncementNotification extends Mailable
{
    public function __construct(public array $announcement, public string $dashboardUrl)
    {
    }

    public function build(): self
    {
        $title = preg_replace('/[\r\n]+/', ' ', $this->announcement['title']);

        return $this->subject('[InternConnect] '.$title)->view('mail.announcement-notification');
    }
}
