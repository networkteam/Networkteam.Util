<?php
namespace Networkteam\Util\Mail;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/
interface MailerMessageInterface
{

    /**
     * Returns the mimetype of the message(text/html)
     *
     * @return string
     */
    public function getFormat();

    /**
     * Returns the message body, can be either HTML or text
     *
     * @return string
     */
    public function getBody();

    /**
     * @return mixed
     */
    public function getFrom();

    /**
     * @return string
     */
    public function getReplyTo();

    /**
     * @return array An array of recipient addresses (string or array)
     */
    public function getRecipient();

    /**
     * @return string
     */
    public function getSubject();

    /**
     * @return string A recipient identifier (not necessarily an email address) for logging
     */
    public function getRecipientIdentifier();

    /**
     * @return array<MailHeader>
     */
    public function getHeaders();
}
