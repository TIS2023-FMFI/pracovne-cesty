<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SimpleMail extends Mailable
{
    use Queueable, SerializesModels;

    public $messageText;
    public $recipient;

    public function __construct($messageText, $recipient)
    {
        $this->messageText = $messageText;
        $this->recipient = $recipient;
    }

    public function build()
    {
        return $this->view('emails.simple')
            ->subject('Pracovné cesty')
            ->with([
                'messageText' => $this->messageText,
            ])
            ->to($this->recipient);
    }
}
