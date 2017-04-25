<?php
namespace Networkteam\Util\Factory;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

use JMS\Serializer\SerializerBuilder;
use TYPO3\Flow\Annotations as Flow;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

/**
 * @Flow\Scope("singleton")
 */
class SerializerFactory {

	/**
	 * @var \Symfony\Component\Serializer\Serializer
	 */
	protected $serializer;

	/**
	 * @var \TYPO3\Flow\ObjectManagement\ObjectManagerInterface
	 * @Flow\Inject
	 */
	protected $objectManager;

	/**
	 * @return \Symfony\Component\Serializer\Serializer
	 */
	public function createJsonSerializer() {
		if (isset($this->serializer['json']) && $this->serializer['json']) {
			return $this->serializer['json'];
		}

		$normalizers[] = $this->objectManager->get('Networkteam\Util\Serializer\Normalizer\FlowIdentityGetSetNormalizer');

		$encoders[] = new JsonEncoder();

		return $this->serializer['json'] = new Serializer($normalizers, $encoders);
	}

	/**
	 * @return \JMS\Serializer\Serializer
	 */
	public function createJmsSerializer() {
		if (isset($this->serializer['jms']) && $this->serializer['jms']) {
			return $this->serializer['jms'];
		}
		$serializer = SerializerBuilder::create()->build();

		return $this->serializer['jms'] = $serializer;
	}

}
