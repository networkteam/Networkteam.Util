<?php
namespace Networkteam\Util\Factory;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

use JMS\Serializer\SerializerBuilder;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\ObjectManagement\ObjectManagerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

/**
 * @Flow\Scope("singleton")
 */
class SerializerFactory
{

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @var ObjectManagerInterface
     * @Flow\Inject
     */
    protected $objectManager;

    public function createJsonSerializer(): Serializer
    {
        if (isset($this->serializer['json']) && $this->serializer['json']) {
            return $this->serializer['json'];
        }

        $normalizers[] = $this->objectManager->get('Networkteam\Util\Serializer\Normalizer\FlowIdentityGetSetNormalizer');

        $encoders[] = new JsonEncoder();

        return $this->serializer['json'] = new Serializer($normalizers, $encoders);
    }

    public function createJmsSerializer(): \JMS\Serializer\Serializer
    {
        if (isset($this->serializer['jms']) && $this->serializer['jms']) {
            return $this->serializer['jms'];
        }
        $serializer = SerializerBuilder::create()->build();

        return $this->serializer['jms'] = $serializer;
    }

}
