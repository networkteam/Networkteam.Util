<?php
namespace Networkteam\Util\ViewHelpers;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

use Neos\Flow\Annotations as Flow;
use TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper;

class AuthenticatedAccountViewHelper extends AbstractViewHelper {

	/**
	 * NOTE: This property has been introduced via code migration to ensure backwards-compatibility.
	 * @see AbstractViewHelper::isOutputEscapingEnabled()
	 * @var boolean
	 */
	protected $escapeOutput = FALSE;

	/**
	 * @Flow\Inject
	 * @var \Neos\Flow\Security\Context
	 */
	protected $securityContext;

	/**
	 * @param string $as Variable name to store the account
	 *
	 * @return string
	 */
	public function render($as = 'account') {
		$this->templateVariableContainer->add($as, $this->securityContext->getAccount());
		$output = $this->renderChildren();
		$this->templateVariableContainer->remove($as);
		return $output;
	}
}
