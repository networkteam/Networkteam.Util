<?php
namespace Networkteam\Util\ViewHelpers;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

use Networkteam\Util\Domain\DataTransferObject\ListViewConfiguration;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\ActionRequest;
use TYPO3\Flow\Mvc\Controller\Argument;
use TYPO3\Flow\Utility\Arrays;
use TYPO3\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use TYPO3\Fluid\Exception;

class SortLinkViewHelper extends AbstractTagBasedViewHelper {

	/**
	 * @var string
	 */
	protected $tagName = 'a';

	/**
	 * Initialize arguments
	 *
	 * @return void
	 * @api
	 */
	public function initializeArguments() {
		$this->registerUniversalTagAttributes();
		$this->registerTagAttribute('name', 'string', 'Specifies the name of an anchor');
		$this->registerTagAttribute('rel', 'string', 'Specifies the relationship between the current document and the linked document');
		$this->registerTagAttribute('rev', 'string', 'Specifies the relationship between the linked document and the current document');
		$this->registerTagAttribute('target', 'string', 'Specifies where to open the linked document');
	}

	/**
	 * @param string $sortProperty
	 * @param \Networkteam\Util\Domain\DataTransferObject\ListViewConfiguration $listViewConfiguration
	 * @return string
	 */
	public function render($sortProperty, ListViewConfiguration $listViewConfiguration) {
		$uriBuilder = $this->controllerContext->getUriBuilder();

		$className = 'sort-asc';
		$sortDirection = 'ASC';
		$iconHtml = '';

		if ($listViewConfiguration->getSortProperty() === $sortProperty) {
			if ($listViewConfiguration->getSortDirection() === 'ASC') {
				$className = 'sorted sort-asc';
				$sortDirection = 'DESC';
				$iconHtml = '<i class="icon-chevron-up"></i>';
			} else {
				$className = 'sorted sort-desc';
				$sortDirection = 'ASC';
				$iconHtml = '<i class="icon-chevron-down"></i>';
			}
		}

		$this->tag->addAttribute('class', $this->tag->getAttribute('class') . $className);

		$linkViewConfigurationArguments = array('listViewConfiguration' => array('sortProperty' => $sortProperty, 'sortDirection' => $sortDirection));

		/** @var  $request \TYPO3\Flow\Mvc\ActionRequest */
		$request = $this->controllerContext->getRequest();

		$arguments = Arrays::arrayMergeRecursiveOverrule($request->getArguments(), $linkViewConfigurationArguments);

		$uri = $uriBuilder->reset()->uriFor($request->getControllerActionName(), $arguments);

		$this->tag->addAttribute('href', $uri);

		$this->tag->setContent($this->renderChildren() . $iconHtml);
		$this->tag->forceClosingTag(TRUE);

		return $this->tag->render();
	}

}

?>