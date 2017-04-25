<?php
namespace Networkteam\Util\Property\TypeConverter;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Property\TypeConverter\AbstractTypeConverter;

/**
 * Converter which transforms arrays to arrays.
 *
 * @Flow\Scope("singleton")
 */
class DeepArrayConverter extends AbstractTypeConverter {

	/**
	 * @var array<string>
	 */
	protected $sourceTypes = array('array');

	/**
	 * @var string
	 */
	protected $targetType = 'array';

	/**
	 * @var integer
	 */
	protected $priority = -100;

	/**
	 * Actually convert from $source to $targetType, in fact a noop here.
	 *
	 * @param array $source
	 * @param string $targetType
	 * @param array $convertedChildProperties
	 * @param \Neos\Flow\Property\PropertyMappingConfigurationInterface $configuration
	 * @return array
	 */
	public function convertFrom($source, $targetType, array $convertedChildProperties = array(), \Neos\Flow\Property\PropertyMappingConfigurationInterface $configuration = NULL) {
		return $convertedChildProperties;
	}

	/**
	 * Returns the source, if it is an array, otherwise an empty array.
	 *
	 * @param mixed $source
	 * @return array
	 */
	public function getSourceChildPropertiesToBeConverted($source) {
		return $source;
	}

	/**
	 * Return the type of a given sub-property inside the $targetType
	 *
	 * @param string $targetType
	 * @param string $propertyName
	 * @param \Neos\Flow\Property\PropertyMappingConfigurationInterface $configuration
	 * @return string
	 */
	public function getTypeOfChildProperty($targetType, $propertyName, \Neos\Flow\Property\PropertyMappingConfigurationInterface $configuration) {
		$parsedTargetType = \Neos\Utility\TypeHandling::parseType($targetType);
		return $parsedTargetType['elementType'];
	}
}
