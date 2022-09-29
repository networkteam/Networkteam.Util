<?php
namespace Networkteam\Util\Validation\Validator;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Validation\Validator\AbstractValidator;

class PasswordValidator extends AbstractValidator {

	protected $supportedOptions = array(
		'allowEmpty' => array(FALSE, 'If set the validation will not fail if the value is empty, used for updating records when not setting a new password', FALSE),
		'minimumLength' => array(8, 'Minumum length of password', FALSE)
	);

	/**
	 * Check if $value is valid. If it is not valid, needs to add an error
	 * to Result.
	 *
	 * passwords should be provided as array
	 *
	 * @param mixed $value
	 * @return void
	 */
	protected function isValid($value) {
		if (!is_array($value) || count($value) < 2) {
			$this->addError('The given value was not an array of passwords.', 1372340267);
			return;
		}

		// get the option for the validation
		$minimumLength = $this->options['minimumLength'];

		// check for empty password
		if ($value[0] === '' && !$this->options['allowEmpty']) {
			$this->addError('The password is to weak.', 1221560717);
		}
		// check for password length
		if (strlen($value[0]) < $minimumLength && !$this->options['allowEmpty']) {
			$this->addError('The password is to short, minimum length is ' . $minimumLength, 1221560719, array($minimumLength));
		}

		// check that the passwords are the same
		if (strcmp($value[0], $value[1]) != 0) {
			$this->addError('The password do not match!', 1372861059);
		}
	}
}
