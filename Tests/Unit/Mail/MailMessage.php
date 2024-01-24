<?php

namespace Networkteam\Util\Tests\Unit\Mail;

use Networkteam\Util\Mail\MailHeader;

/***************************************************************
 *  (c) 2016 networkteam GmbH - all rights reserved
 ***************************************************************/
class MailMessage implements \Networkteam\Util\Mail\MailerMessageInterface
{

    /**
     * @var array
     */
    protected $recipients;

    /**
     * @var string
     */
    protected $replyTo;


    /**
     * @var array<MailHeader>
     */
    protected array $headers;

    /**
     * Returns the mimetype of the message(text/html)
     *
     * @return string
     */
    public function getFormat()
    {
        // TODO: Implement getFormat() method.
    }

    /**
     * Returns the message body, can be either HTML or text
     *
     * @return string
     */
    public function getBody()
    {
        // TODO: Implement getBody() method.
    }

    /**
     * @return mixed
     */
    public function getFrom()
    {
        // TODO: Implement getFrom() method.
    }

    /**
     * @return array An array of recipient addresses (string or array)
     */
    public function getRecipient()
    {
        return $this->recipients;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        // TODO: Implement getSubject() method.
    }

    /**
     * @return array<MailHeader>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @return string A recipient identifier (not necessarily an email address) for logging
     */
    public function getRecipientIdentifier()
    {
        // TODO: Implement getRecipientIdentifier() method.
    }

    public function getReplyTo()
    {
        return $this->replyTo;
    }

    /**
     * @param mixed $recipients
     */
    public function setRecipients($recipients)
    {
        $this->recipients = $recipients;
    }

    /**
     * @param string $replyTo
     */
    public function setReplyTo($replyTo)
    {
        $this->replyTo = $replyTo;
    }

    /**
     * @param array<string> $headers
     * @return void
     */
    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }
}
