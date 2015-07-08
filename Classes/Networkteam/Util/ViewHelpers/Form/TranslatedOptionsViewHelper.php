<?php
namespace Networkteam\Util\ViewHelpers\Form;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

use TYPO3\Flow\Annotations as Flow;

class TranslatedOptionsViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {

	
	/**
	 * NOTE: This property has been introduced via code migration to ensure backwards-compatibility.
	 * @see AbstractViewHelper::isOutputEscapingEnabled()
	 * @var boolean
	 */
	protected $escapeOutput = FALSE;

	/**
	 * @var \TYPO3\Flow\I18n\Translator
	 * @Flow\Inject
	 */
	protected $translator;

	/**
	 *
	 * @param string $prefix Prefix for translation ids (e.g. "options.foo")
	 * @param boolean $translateById
	 * @param string $package
	 * @param string $sourceName
	 * @param string $locale
	 * @throws \TYPO3\Fluid\Core\ViewHelper\Exception
	 * @return array
	 */
	public function render($prefix = NULL, $translateById = TRUE, $package = NULL, $sourceName = 'Main', $locale = NULL) {
		if ($package === NULL) $package = $this->controllerContext->getRequest()->getControllerPackageKey();

		$localeObject = NULL;
		if ($locale !== NULL) {
			try {
				$localeObject = new \TYPO3\Flow\I18n\Locale($locale);
			} catch (\TYPO3\Flow\I18n\Exception\InvalidLocaleIdentifierException $e) {
				throw new \TYPO3\Fluid\Core\ViewHelper\Exception('"' . $locale . '" is not a valid locale identifier.' , 1372342505);
			}
		}

		$options = $this->renderChildren();
		foreach ($options as $value => &$label) {
			if ($translateById) {
				$labelId = (string)$prefix !== '' ? $prefix . '.' . $label : $label;
				$label = $this->translator->translateById($labelId, array(), NULL, $localeObject, $sourceName, $package);
			} else {
				$label = $this->translator->translateByOriginalLabel($label, array(), NULL, $localeObject, $sourceName, $package);
			}
		}

		return $options;
	}
}
