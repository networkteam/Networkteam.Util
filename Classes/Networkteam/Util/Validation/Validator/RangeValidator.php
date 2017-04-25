<?php
namespace Networkteam\Util\Validation\Validator;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

use Neos\Flow\Validation\Validator\AbstractValidator;

/**
 * A number range validator
 */
class RangeValidator extends AbstractValidator {

	/**
	 * @var array
	 */
	protected $supportedOptions = array(
		'minimum' => array(0, 'The minimum number to accept', 'integer'),
		'maximum' => array(PHP_INT_MAX, 'The maximum number to accept', 'integer')
	);

	/**
	 * Check if $value is valid. If it is not valid, needs to add an error
	 * to Result.
	 *
	 * @param mixed $value
	 *
	 * @return void
	 * @throws \Neos\Flow\Validation\Exception\InvalidValidationOptionsException if invalid validation options have been specified in the constructor
	 */
	protected function isValid($value) {
		$minimum = intval($this->options['minimum']);
		$maximum = intval($this->options['maximum']);
		if ((int)$value < $minimum || (int)$value > $maximum) {
			$this->addError('The value must be between %1$d and %2$d.', 1365773104, array($minimum, $maximum));
		}
	}
}
