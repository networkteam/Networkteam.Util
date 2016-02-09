<?php
namespace Networkteam\Util\Tests\Unit\Mail;

/***************************************************************
 *  (c) 2015 networkteam GmbH - all rights reserved
 ***************************************************************/

use TYPO3\Flow\Tests\UnitTestCase;

class MailerTest extends UnitTestcase {

	/**
	 * @@test
	 */
	public function mailerUsesAddCcForAddingRecipients() {
		$mailer = new \Networkteam\Util\Mail\Mailer();
		$message = $this->getMockBuilder('TYPO3\SwiftMailer\Message')
			->setMethods(array('setFrom', 'setTo', 'addCc', 'setBody', 'send', 'setSubject', 'getTo', 'getSubject', 'getRecipientIdentifier', 'setReplyTo'))
			->getMock();

		$message->expects($this->exactly(2))
			->method('addCc');

		$message->expects($this->exactly(1))
			->method('setReplyTo')
		->with($this->equalTo('my-reply@example.com'));

		$logger = $this->getMock('Networkteam\Util\Log\MailerLoggerInterface');
		$this->inject($mailer, 'logger', $logger);

		$mailer->setMessage($message);
		$mailMessage = new MailMessage();
		$mailer->send($mailMessage);
	}
}

class MailMessage implements \Networkteam\Util\Mail\MailerMessageInterface {

	/**
	 * Returns the mimetype of the message(text/html)
	 *
	 * @return string
	 */
	public function getFormat() {
		// TODO: Implement getFormat() method.
	}

	/**
	 * Returns the message body, can be either HTML or text
	 *
	 * @return string
	 */
	public function getBody() {
		// TODO: Implement getBody() method.
	}

	/**
	 * @return mixed
	 */
	public function getFrom() {
		// TODO: Implement getFrom() method.
	}

	/**
	 * @return array An array of recipient addresses (string or array)
	 */
	public function getRecipient() {
		return array(
			'test1@example.com',
			'test2@example.com',
			'test3@example.com',
		);
	}

	/**
	 * @return string
	 */
	public function getSubject() {
		// TODO: Implement getSubject() method.
	}

	/**
	 * @return string A recipient identifier (not necessarily an email address) for logging
	 */
	public function getRecipientIdentifier() {
		// TODO: Implement getRecipientIdentifier() method.
	}

	public function getReplyTo() {
		return 'my-reply@example.com';
	}
}
