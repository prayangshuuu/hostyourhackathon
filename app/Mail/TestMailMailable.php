<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestMailMailable extends Mailable
{
    use Queueable, SerializesModels;

    public function build(): self
    {
        return $this->subject('HostYourHackathon — Mail Test')
            ->text('emails.test-mail');
    }
}
