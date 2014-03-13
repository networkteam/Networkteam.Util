<?php
namespace Networkteam\Util\Serializer\Normalizer;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

use TYPO3\Flow\Annotations as Flow;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

class FlowIdentityGetSetNormalizer extends GetSetMethodNormalizer {

	/**
	 * @var \TYPO3\Flow\Persistence\PersistenceManagerInterface
	 * @Flow\Inject
	 */
	protected $persistenceManager;

	/**
	 * @param object $object
	 * @param null $format
	 * @param array $context
	 *
	 * @return array|\Symfony\Component\Serializer\Normalizer\scalar
	 */
	public function normalize($object, $format = NULL, array $context = array()) {
		$attributes = parent::normalize($object, $format, $context);

		$identifier = $this->persistenceManager->getIdentifierByObject($object);
		if ($identifier) {
			$attributes['__identifier'] = $identifier;
		}

		return $attributes;
	}
}
