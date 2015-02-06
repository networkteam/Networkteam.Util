<?php
namespace Networkteam\Util\Mail;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

use TYPO3\Flow\Annotations as Flow;

abstract class AbstractRenderingMessage implements MailerMessageInterface {

	/**
	 * @var mixed
	 */
	protected $from;

	/**
	 * @var mixed
	 */
	protected $recipient;

	/**
	 * @var string
	 */
	protected $subject;

	/**
	 * @var string
	 */
	protected $templatePathAndFilename;

	/**
	 * @var array
	 */
	protected $templateData;

	/**
	 * @var array
	 */
	protected $options;

	/**
	 * @var string
	 */
	protected $recipientIdentifier = '';

	/**
	 * @var \TYPO3\Flow\Configuration\ConfigurationManager
	 * @Flow\Inject
	 */
	protected $configurationManager;

	/**
	 * @param string $templatePathAndFilename
	 */
	public function __construct($templatePathAndFilename) {
		$this->options['templatePathAndFilename'] = $templatePathAndFilename;
	}

	/**
	 * Returns the message Body, can be either html or text
	 *
	 * @return string
	 */
	public function getBody() {
		$view = $this->createStandaloneView();
		$view->assignMultiple($this->templateData);

		return $view->render();
	}

	/**
	 * @return mixed
	 */
	public function getFrom() {
		return $this->from;
	}

	/**
	 * @return array
	 */
	public function getRecipient() {
		return $this->recipient;
	}

	/**
	 * @return string
	 */
	public function getSubject() {
		return $this->subject;
	}

	/**
	 * @param array $options
	 */
	public function setOptions(array $options) {
		$this->options = $options;
	}

	/**
	 * @param array $recipient
	 */
	public function setRecipient($recipient) {
		$this->recipient = $recipient;
	}

	/**
	 * @param mixed $from
	 */
	public function setFrom($from) {
		$this->from = $from;
	}

	/**
	 * @param string $subject
	 */
	public function setSubject($subject) {
		$this->subject = $subject;
	}

	/**
	 * @param array $templateData
	 */
	public function setTemplateData($templateData) {
		$this->templateData = $templateData;
	}

	/**
	 * @return \TYPO3\Fluid\View\StandaloneView
	 * @throws MissingArgumentException
	 */
	protected function createStandaloneView() {
		$standaloneView = new \TYPO3\Fluid\View\StandaloneView();
		if (!isset($this->options['templatePathAndFilename'])) {
			// TODO change Exception
			throw new MissingArgumentException('The option "templatePathAndFilename" must be set for the AbstractRenderingMessage.', 1327058829);
		}
		$standaloneView->setTemplatePathAndFilename($this->options['templatePathAndFilename']);

		if (isset($this->options['partialRootPath'])) {
			$standaloneView->setPartialRootPath($this->options['partialRootPath']);
		}

		if (isset($this->options['layoutRootPath'])) {
			$standaloneView->setLayoutRootPath($this->options['layoutRootPath']);
		}

		if (isset($this->options['variables'])) {
			$standaloneView->assignMultiple($this->options['variables']);
		}

		$actionRequest = $standaloneView->getRequest();
		$flowSettings = $this->configurationManager->getConfiguration(\TYPO3\Flow\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'TYPO3.Flow');
		if (isset($flowSettings['http']['baseUri'])) {
			$actionRequest->getHttpRequest()->setBaseUri($flowSettings['http']['baseUri']);
		}

		return $standaloneView;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getRecipientIdentifier() {
		return $this->recipientIdentifier;
	}

	/**
	 * @param string $recipientIdentifier
	 */
	public function setRecipientIdentifier($recipientIdentifier) {
		$this->recipientIdentifier = $recipientIdentifier;
	}

}
