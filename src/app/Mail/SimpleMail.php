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
    public $viewTemplate;

    public function __construct($messageText, $recipient, $viewTemplate)
    {
        $this->messageText = $messageText;
        $this->recipient = $recipient;
        $this->viewTemplate = $viewTemplate;
    }

    public function build()
    {
        $textTemplate = $this->viewTemplate . '_text';

        return $this->view($this->viewTemplate)
            ->text($textTemplate)
            ->subject('Pracovné cesty')
            ->with([
                'messageText' => $this->messageText,
            ])
            ->to($this->recipient);
    }
}
