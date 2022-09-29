<?php
namespace Networkteam\Util\Mail;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

use Neos\Error\Messages\Result;

/**
 * Mailer interface
 */
interface MailerInterface {

	/**
	 * @param \Networkteam\Util\Mail\MailerMessageInterface $message
	 * @return \Neos\Error\Messages\Result
	 */
	public function send(MailerMessageInterface $message);

}
