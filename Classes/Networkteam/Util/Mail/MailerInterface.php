<?php
namespace Networkteam\Util\Mail;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

use TYPO3\Flow\Error\Result;

/**
 * Mailer interface
 */
interface MailerInterface {

	/**
	 * @param \Networkteam\Util\Mail\MailerMessageInterface $message
	 * @return \TYPO3\Flow\Error\Result
	 */
	public function send(MailerMessageInterface $message);

}
