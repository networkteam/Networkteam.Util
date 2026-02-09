<?php
namespace Networkteam\Util\Serializer\Normalizer;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\PersistenceManagerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

class FlowIdentityGetSetNormalizer extends AbstractObjectNormalizer
{

    /**
     * @var PersistenceManagerInterface
     * @Flow\Inject
     */
    protected $persistenceManager;

    /**
     * @var GetSetMethodNormalizer
     * @Flow\Inject
     */
    protected $getSetMethodNormalizer;


    public function normalize(mixed $data, $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        $attributes = parent::normalize($data, $format, $context);

        $identifier = $this->persistenceManager->getIdentifierByObject($data);
        if ($identifier) {
            $attributes['__identifier'] = $identifier;
        }

        return $attributes;
    }

    protected function extractAttributes(object $object, ?string $format = null, array $context = []): array
    {
        return $this->getSetMethodNormalizer->extractAttributes($object, $format, $context);
    }

    protected function getAttributeValue(object $object, string $attribute, ?string $format = null, array $context = []): mixed
    {
        return $this->getSetMethodNormalizer->getAttributeValue($object, $attribute, $format, $context);
    }

    protected function setAttributeValue(object $object, string $attribute, mixed $value, ?string $format = null, array $context = []): void
    {
        $this->getSetMethodNormalizer->setAttributeValue($object, $attribute, $value, $format, $context);
    }

    public function getSupportedTypes(?string $format): array
    {
        return $this->getSetMethodNormalizer->getSupportedTypes($format);
    }
}
