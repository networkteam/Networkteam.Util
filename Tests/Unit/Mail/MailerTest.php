<?php

namespace Networkteam\Util\Tests\Unit\Mail;

/***************************************************************
 *  (c) 2015 networkteam GmbH - all rights reserved
 ***************************************************************/

use Neos\Flow\Tests\UnitTestCase;
use Psr\Log\LoggerInterface;


class MailerTest extends UnitTestCase
{

	/**
	 * @var \Neos\SwiftMailer\Message
	 */
	protected $mockMessage;

	/**
	 * @var \Networkteam\Util\Mail\MailerInterface
	 */
	protected $mailer;

	public function setUp(): void
	{
		parent::setUp();
		$this->mockMessage = $this->getMockBuilder('Neos\SwiftMailer\Message')
			->setMethods(array('setFrom', 'setTo', 'addCc', 'setBody', 'send', 'setSubject', 'getTo', 'getSubject', 'getRecipientIdentifier', 'setReplyTo'))
			->getMock();
		$this->mailer = new \Networkteam\Util\Mail\Mailer();

		$this->mailer->setMessage($this->mockMessage);

		$logger = $this->getMockBuilder('Networkteam\Util\Log\MailerLoggerInterface')
			->getMock();

		$this->inject($this->mailer, 'logger', $logger);
	}

	/**
	 * @@test
	 */
	public function mailerUsesAddCcForAddingRecipients()
	{
		$this->mockMessage->expects($this->exactly(2))
			->method('addCc');

		$this->mockMessage->expects($this->exactly(1))
			->method('setReplyTo')
			->with($this->equalTo('my-reply@example.com'));

		$mailMessage = new MailMessage();
		$mailMessage->setRecipients(array(
			'test1@example.com',
			'test2@example.com',
			'test3@example.com',
		));

		$mailMessage->setHeaders([]);

		$mailMessage->setReplyTo('my-reply@example.com');

		$this->mailer->send($mailMessage);
	}

	/**
	 * @@test
	 */
	public function mailerSkipsReplyToIfEmpty()
	{
		$this->mockMessage->expects($this->never())
			->method('setReplyTo');

		$mailMessage = new MailMessage();
		$mailMessage->setRecipients(array(
			'test1@example.com'
		));

		$mailMessage->setHeaders([]);

		$this->mailer->send($mailMessage);
	}
}
