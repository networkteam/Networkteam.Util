<?php
namespace Networkteam\Util\ViewHelpers\Form;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

use Neos\Error\Messages\Result;
use Neos\FluidAdaptor\Core\Rendering\RenderingContext;
use Neos\FluidAdaptor\Core\ViewHelper\AbstractConditionViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

class HasValidationResultsViewHelper extends AbstractConditionViewHelper
{

	public function initializeArguments()
	{
		$this->registerArgument('then', 'mixed', 'Value to be returned if the condition if met.', false);
		$this->registerArgument('else', 'mixed', 'Value to be returned if the condition if not met.', false);
		$this->registerArgument('for', 'string', 'The name of the error name (e.g. argument name or property name). This can also be a property path (like blog.title), and will then only display the validation errors of that property.', false, null);
	}

	public function render()
	{
		if (static::evaluateCondition($this->arguments, $this->renderingContext)) {
			return $this->renderThenChild();
		}

		return $this->renderElseChild();
	}

	protected static function evaluateCondition(
		$arguments,
		RenderingContextInterface $renderingContext
	)
	{

		if (!$renderingContext instanceof RenderingContext) {
			return false;
		}

		$controllerContext = $renderingContext->getControllerContext();

		/** @var Result $validationResults */
		$validationResults = $controllerContext->getRequest()->getInternalArgument('__submittedArgumentValidationResults');

		$forProperty = $arguments['for'] ?? null;

		if ($validationResults !== null && $forProperty !== null) {
			$validationResults = $validationResults->forProperty($forProperty);
		}

		return $validationResults !== null && $validationResults->hasErrors();
	}
}
