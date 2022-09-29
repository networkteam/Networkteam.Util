<?php
namespace Networkteam\Util\Serializer\Normalizer;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\PersistenceManagerInterface;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

class FlowIdentityGetSetNormalizer extends GetSetMethodNormalizer
{

    /**
     * @var PersistenceManagerInterface
     * @Flow\Inject
     */
    protected $persistenceManager;

    /**
     * @param object $object
     * @param string $format
     * @param array $context
     * @return array
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $attributes = parent::normalize($object, $format, $context);

        $identifier = $this->persistenceManager->getIdentifierByObject($object);
        if ($identifier) {
            $attributes['__identifier'] = $identifier;
        }

        return $attributes;
    }
}
