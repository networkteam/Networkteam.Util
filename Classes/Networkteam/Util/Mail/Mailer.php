<?php
namespace Networkteam\Util\Mail;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

use TYPO3\Flow\Error\Error;
use TYPO3\Flow\Error\Result;
use TYPO3\SwiftMailer\Message;
use TYPO3\Flow\Annotations as Flow;

/**
 * Mailer implementation using SwiftMailer
 *
 * TODO Rename to SwiftMailerMailer
 */
class Mailer implements MailerInterface {

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
	 * @var
	 */
	protected $message;

	/**
	 * Inject the settings
	 *
	 * @param array $settings
	 * @return void
	 */
	public function injectSettings(array $settings) {
		$this->settings = $settings;
	}

	/**
	 * @param \Networkteam\Util\Mail\MailerMessageInterface $message
	 * @return \TYPO3\Flow\Error\Result
	 */
	public function send(MailerMessageInterface $message) {
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

		if ($result->hasErrors()) {
			$logMessage = 'Failed sending mail to "' . implode('", "', array_keys((array)$mail->getTo())) . '" (' . $message->getRecipientIdentifier() . ') with subject "' . $mail->getSubject() . '"';
			$flattenedErrors = $result->getFlattenedErrors();
			if ($flattenedErrors !== array()) {
				$additionalData = array();
				foreach ($flattenedErrors as $propertyPath => $errors) {
					$additionalData[$propertyPath] = implode(', ', array_map(function ($error) {
						return $error->getMessage();
					}, $errors));
				}
			} else {
				$additionalData = NULL;
			}
			$this->logger->log($logMessage, LOG_ERR, $additionalData);
		} else {
			if (isset($this->settings['Mailer']['bcc'])) {
				foreach ($this->settings['Mailer']['bcc'] as $bccMail) {
					$mail->addBcc($bccMail);
				}
			}
			try {
				$recipients = $mail->send();
				if ($recipients === 0) {
					$result->addError(new Error('No recipients accepted', 1376582260));
					$logMessage = 'No Recipients accepted: ' . implode(',', $mail->getFailedRecipients());
				} else {
					$logMessage = 'Send mail to: ' . implode(',', array_keys($mail->getTo())) . ' with subject: ' . $mail->getSubject();
				}
				$this->logger->log($logMessage, LOG_INFO);
			} catch (\Exception $e) {
				$logMessage = 'Failed sending mail: ' . $e->getMessage();

				$this->logger->log($logMessage, LOG_ERR);
				$this->logger->logException($e);
			}
		}

		return $result;
	}

	/**
	 * @param Message $message
	 */
	public function setMessage(Message $message) {
		$this->message = $message;
	}

	/**
	 * @return Message
	 */
	protected function getMessage() {
		if ($this->message instanceof Message) {
			return $this->message;
		}

		return new Message();
	}
}
