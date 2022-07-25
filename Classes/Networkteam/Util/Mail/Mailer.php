<?php

namespace Networkteam\Util\Mail;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

use Neos\Error\Messages\Error;
use Neos\Error\Messages\Result;
use Neos\SwiftMailer\Message;
use Neos\Flow\Annotations as Flow;

/**
 * Mailer implementation using SwiftMailer
 *
 * TODO Rename to SwiftMailerMailer
 */
class Mailer implements MailerInterface
{

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * @var \Networkteam\Util\Log\MailerLoggerInterface
	 * @Flow\Inject
	 */
	protected $logger;

	/**
	 * @var \Neos\Flow\Log\ThrowableStorageInterface
	 * @Flow\Inject
	 */
	protected $throwableStorage;

	/**
	 * @var
	 */
	protected $message;

	/**
	 * Inject the settings
	 *
	 * @param array $settings
	 * @return void
	 */
	public function injectSettings(array $settings)
	{
		$this->settings = $settings;
	}

	/**
	 * @param \Networkteam\Util\Mail\MailerMessageInterface $message
	 * @return \Neos\Error\Messages\Result
	 */
	public function send(MailerMessageInterface $message)
	{
		$result = new Result();

		$mail = $this->getMessage();

		try {
			$mail->setFrom($message->getFrom());
		} catch (\Swift_RfcComplianceException $exception) {
			$result->forProperty('sender')->addError(new Error($exception->getMessage(), 1365160468));
		}

		try {
			$recipientCount = 0;
			foreach ($message->getRecipient() as $recipient) {
				if ($recipientCount < 1) {
					$mail->setTo($recipient);
				} else {
					$mail->addCc($recipient);
				}
				$recipientCount++;
			}
		} catch (\Swift_RfcComplianceException $exception) {
			$result->forProperty('recipient')->addError(new Error($exception->getMessage(), 1365160498));
		}

		if ((string)$message->getSubject() === '') {
			$result->forProperty('subject')->addError(new Error('Missing subject for mail', 1365172209));
		}

		$mail->setSubject($message->getSubject());

		$mail->setBody($message->getBody(), $message->getFormat());

		if ($message->getReplyTo()) {
			$mail->setReplyTo($message->getReplyTo());
		}

		foreach ($message->getHeaders() as $header) {
			$mail->getHeaders()->addTextHeader($header->getName(), $header->getValue());
		}

		if ($result->hasErrors()) {
			$logMessage = 'Failed sending mail to "' . implode('", "',
					array_keys((array)$mail->getTo())) . '" (' . $message->getRecipientIdentifier() . ') with subject "' . $mail->getSubject() . '"';
			$flattenedErrors = $result->getFlattenedErrors();
			if ($flattenedErrors !== array()) {
				$additionalData = array();
				foreach ($flattenedErrors as $propertyPath => $errors) {
					$additionalData[$propertyPath] = implode(', ', array_map(function ($error) {
						return $error->getMessage();
					}, $errors));
				}
			} else {
				$additionalData = null;
			}
			$this->logger->error($logMessage, $additionalData);
		} else {
			if (isset($this->settings['Mailer']['bcc'])) {
				foreach ($this->settings['Mailer']['bcc'] as $bccMail) {
					$mail->addBcc($bccMail);
				}
			}

			if (isset($this->settings['Mailer']['overrideRecipients'])) {
				$mail->setBcc([]);
				$mail->setCc([]);
				$mail->setTo($this->settings['Mailer']['overrideRecipients']);
			}

			try {
				$recipients = $mail->send();
				if ($recipients === 0) {
					$result->addError(new Error('No recipients accepted', 1376582260));
					$logMessage = 'No Recipients accepted: ' . implode(',', $mail->getFailedRecipients());
				} else {
					$logMessage = 'Sent mail to: ' . implode(',',
							array_keys($mail->getTo())) . ' with subject: ' . $mail->getSubject();
				}
				$this->logger->info($logMessage);
			} catch (\Exception $e) {
				$message = $this->throwableStorage->logThrowable($e);
				$this->logger->error('Failed sending mail: ' . $message);

				$result->addError(new Error('Error sending email', 1438095110));
			}
		}

		return $result;
	}

	/**
	 * @param Message $message
	 */
	public function setMessage(Message $message)
	{
		$this->message = $message;
	}

	/**
	 * @return Message
	 */
	protected function getMessage()
	{
		if ($this->message instanceof Message) {
			return $this->message;
		}

		return new Message();
	}
}
