<?php
namespace Networkteam\Util\Mail;

use TYPO3\Flow\Error\Error;
use TYPO3\Flow\Error\Result;
use TYPO3\SwiftMailer\Message;
use TYPO3\Flow\Annotations as Flow;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

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

		$mail = new Message();

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
					$mail->setCc($recipient);
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
			$severity = LOG_ERR;
			$logMessage = 'Failed sending mail to: ' . implode(',', array_keys((array)$mail->getTo())) . ' with subject: ' . $mail->getSubject();
			if ($result->getFirstError() !== FALSE) {
				$logMessage .= ' First Error: ' . $result->getFirstError()->getMessage();
			}
		} else {
			if (isset($this->settings['Mailer']['bcc'])) {
				foreach ($this->settings['Mailer']['bcc'] as $bccMail) {
					$mail->addBcc($bccMail);
				}
			}
			try {
				$recipients = $mail->send();
				$severity = LOG_INFO;
				if ($recipients === 0) {
					$result->addError(new Error('No recipients accepted', 1376582260));
					$logMessage = 'No Recipients accepted: ' . implode(',', $mail->getFailedRecipients());
				} else {
					$logMessage = 'Send mail to: ' . implode(',', array_keys($mail->getTo())) . ' with subject: ' . $mail->getSubject();
				}
			} catch (\Exception $e) {
				$logMessage = 'Failed sending mail: ' . $e->getMessage();
				$severity = LOG_ERR;
			}
		}

		$this->logger->log($logMessage, $severity);

		return $result;
	}
}

?>