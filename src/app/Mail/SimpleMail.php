<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * SimpleMail class to send emails with different templates.
 */
class SimpleMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Message text.
     * @var string
     */
    public $messageText;

    /**
     * Recipient's e-mail address.
     * @var string
     */
    public $recipient;

    /**
     * The name of the e-mail display template.
     * @var string
     */
    public $viewTemplate;

    /**
     * The subject line of the email.
     * @var string
     */
    public $subjectLine;

    /**
     * Creates a new instance of SimpleMail.
     * @param string $messageText Message text.
     * @param string $recipient Email address of the recipient.
     * @param string $viewTemplate The name of the template.
     * @param string $subjectLine The subject line for the email.
     */
    public function __construct(string $messageText, string $recipient, string $viewTemplate, string $subjectLine)
    {
        $this->messageText = $messageText;
        $this->recipient = $recipient;
        $this->viewTemplate = $viewTemplate;
        $this->subjectLine = $subjectLine;

    }

    /**
     * Creates an email with the appropriate template, text version and subject line.
     * @return $this
     */
    public function build(): self
    {
        $textTemplate = $this->viewTemplate . '_text';

        return $this->view($this->viewTemplate)
            ->text($textTemplate)
            ->subject($this->subjectLine)
            ->with([
                'messageText' => $this->messageText,
            ])
            ->to($this->recipient);
    }
}
