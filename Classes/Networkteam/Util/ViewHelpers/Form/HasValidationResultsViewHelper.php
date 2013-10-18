<?php
namespace Networkteam\Util\ViewHelpers\Form;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

use TYPO3\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

class HasValidationResultsViewHelper extends AbstractConditionViewHelper {

	/**
	 * Iterates through selected errors of the request.
	 *
	 * @param string $for The name of the error name (e.g. argument name or property name). This can also be a property path (like blog.title), and will then only display the validation errors of that property.
	 * @return string Rendered string
	 * @api
	 */
	public function render($for = '') {
		$validationResults = $this->controllerContext->getRequest()->getInternalArgument('__submittedArgumentValidationResults');
		if ($validationResults !== NULL) {
			$validationResults = $validationResults->forProperty($for);
		}

		if ($validationResults !== NULL && $validationResults->hasErrors()) {
			return $this->renderThenChild();
		} else {
			return $this->renderElseChild();
		}
	}
}

?>