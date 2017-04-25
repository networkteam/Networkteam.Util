<?php
namespace Networkteam\Util\TypeConverter;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Error\Error;

/**
 * An type converter for ResourcePointer objects from JSON based file uploads (no multipart)
 *
 * @Flow\Scope("singleton")
 */
class JsonResourceTypeConverter extends \TYPO3\Flow\Property\TypeConverter\AbstractTypeConverter {

	/**
	 * @var array<string>
	 */
	protected $sourceTypes = array('array');

	/**
	 * @var string
	 */
	protected $targetType = 'TYPO3\Flow\ResourceManagement\PersistentResource';

	/**
	 * @var integer
	 */
	protected $priority = 101;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\ResourceManagement\ResourceManager
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
	 *
	 * @param array $source The upload info (expected keys: filename, value, mime)
	 * @param string $targetType
	 * @param array $convertedChildProperties
	 * @param \TYPO3\Flow\Property\PropertyMappingConfigurationInterface $configuration
	 * @return |TYPO3\Flow\Error\Error if the input format is not supported or could not be converted for other reasons
	 */
	public function convertFrom($source, $targetType, array $convertedChildProperties = array(), \TYPO3\Flow\Property\PropertyMappingConfigurationInterface $configuration = NULL) {
		if (!isset($source['filename']) || !isset($source['value']) || !isset($source['mime'])) {
			return NULL;
		}
		if (strpos($source['value'], 'data:') !== 0) {
			return new Error('Expected data URI as value' , 1369211873);
		}
		$content = file_get_contents($source['value']);
		$resource = $this->resourceManager->createResourceFromContent($content, $source['filename']);
		if ($resource === FALSE) {
			return new Error('The resource manager could not create a Resource instance.' , 1264517906);
		} else {
			return $resource;
		}
	}
}
