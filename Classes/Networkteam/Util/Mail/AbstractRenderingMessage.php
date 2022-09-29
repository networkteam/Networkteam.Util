<?php
namespace Networkteam\Util\Mail;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

use Neos\Flow\Annotations as Flow;

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
	protected $replyTo;

	/**
	 * @var string
	 */
	protected $templatePathAndFilename;

	/**
	 * @var array
	 */
	protected $templateData = array();

	/**
	 * @var array
	 */
	protected $options = array();

	/**
	 * @var string
	 */
	protected $recipientIdentifier = '';

	/**
	 * @var \Neos\Flow\Configuration\ConfigurationManager
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
	 * @return mixed
	 */
	public function getReplyTo() {
		return $this->replyTo;
	}

	/**
	 * @param mixed $replyTo
	 */
	public function setReplyTo($replyTo) {
		$this->replyTo = $replyTo;
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
	public function addTemplateData(array $templateData) {
		$this->templateData = array_merge($this->templateData, $templateData);
	}

	/**
	 * @param array $templateData
	 */
	public function setTemplateData(array $templateData) {
		$this->templateData = $templateData;
	}

	/**
	 * @return \Neos\FluidAdaptor\View\StandaloneView
	 * @throws MissingArgumentException
	 */
	protected function createStandaloneView() {
		$standaloneView = new \Neos\FluidAdaptor\View\StandaloneView();
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

	public function getHeaders()
	{
		return array();
	}
}
