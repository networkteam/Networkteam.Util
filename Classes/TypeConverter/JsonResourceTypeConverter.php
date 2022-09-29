<?php
namespace Networkteam\Util\TypeConverter;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

use Neos\Flow\Annotations as Flow;
use Neos\Error\Messages\Error;
use Neos\Flow\Property\PropertyMappingConfigurationInterface;
use Neos\Flow\ResourceManagement\ResourceManager;

/**
 * An type converter for ResourcePointer objects from JSON based file uploads (no multipart)
 *
 * @Flow\Scope("singleton")
 */
class JsonResourceTypeConverter extends \Neos\Flow\Property\TypeConverter\AbstractTypeConverter {

	/**
	 * @var array<string>
	 */
	protected $sourceTypes = array('array');

	/**
	 * @var string
	 */
	protected $targetType = 'Neos\Flow\ResourceManagement\PersistentResource';

	/**
	 * @var integer
	 */
	protected $priority = 101;

	/**
	 * @Flow\Inject
	 * @var ResourceManager
	 */
	protected $resourceManager;

	/**
	 * @param mixed $source
	 * @param string $targetType
	 * @return boolean
	 */
	public function canConvertFrom($source, $targetType) {
		return isset($source['filename']) && isset($source['value']) && isset($source['mime']);
	}

	/**
	 * Converts the given array into a ResourcePointer
	 */
	public function convertFrom($source, $targetType, array $convertedChildProperties = array(), PropertyMappingConfigurationInterface $configuration = NULL) {
		if (!isset($source['filename']) || !isset($source['value']) || !isset($source['mime'])) {
			return NULL;
		}
		if (strpos($source['value'], 'data:') !== 0) {
			return new Error('Expected data URI as value' , 1369211873);
		}
		$content = file_get_contents($source['value']);
		return $this->resourceManager->importResourceFromContent($content, $source['filename']);
	}
}
