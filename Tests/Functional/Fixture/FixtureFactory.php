<?php
namespace Networkteam\Util\Tests\Functional\Fixture;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

use Neos\Flow\Annotations as Flow;

/**
 * Base test fixture factory
 *
 * @Flow\Scope("singleton")
 */
abstract class FixtureFactory {

	/**
	 * @var string
	 */
	protected $baseType = NULL;

	/**
	 * the identifier for objects in this factory
	 * @var string
	 */
	protected $identifierPropertyName;

	/**
	 *
	 * @var array
	 */
	protected $fixtureDefinitions = array(
		/*
		 * Example configuration
		 *
		'sfmService' => array(
			'__type' => 'Networkteam\MyRossmann\Domain\Model\SfmService',
			'identifier' => 'FooService'
		)
		 */
	);

	/**
	 * @Flow\Inject
	 * @var \Neos\Flow\Persistence\PersistenceManagerInterface
	 */
	protected $persistenceManager;

	/**
	 * place property names to ignore in reflection setting
	 *
	 * @var array
	 */
	protected $ignoredProperties = array();

	/**
	 * Instance cache for created objects identified by this->identifierPropertyName
	 *
	 * @var array
	 */
	protected $instances = array();

	/**
	 *
	 * @param string $objectName
	 * @param array $overrideProperties
	 * @return object
	 */
	public function buildObject($objectName, $overrideProperties = array(), $addObjectToPersistence = FALSE) {
		if (!isset($this->fixtureDefinitions[$objectName])) {
			throw new \Exception('Object name ' . $objectName . ' not configured in fixture definitions');
		}
		$properties = array_merge($this->fixtureDefinitions[$objectName], $overrideProperties);
		$className = isset($properties['__type']) ? $properties['__type'] : $this->baseType;
		unset($properties['__type']);

		$object = new $className();
		foreach ($properties as $propertyName => $propertyValue) {
			if (\Neos\Flow\Reflection\ObjectAccess::isPropertySettable($object, $propertyName) && !in_array($propertyName, $this->ignoredProperties)) {
				\Neos\Flow\Reflection\ObjectAccess::setProperty($object, $propertyName, $propertyValue);
			}
		}

		$this->setCustomProperties($object, $properties);

		if ($addObjectToPersistence) {
			$this->addObjectToPersistence($object);
		}

		$this->instances[$this->createCacheIdentifier($properties[$this->identifierPropertyName])] = $object;

		return $object;
	}

	/**
	 *
	 * @param string $objectName
	 * @param array $overrideProperties
	 * @return object
	 */
	public function createObject($objectName, $overrideProperties = array()) {
		$object = $this->buildObject($objectName, $overrideProperties, TRUE);
		return $object;
	}

	/**
	 * @param object $object
	 * @return void
	 */
	protected function addObjectToPersistence($object) {
		$this->persistenceManager->add($object);
	}

	/**
	 *
	 * @param string $name
	 * @param array $arguments
	 * @return object
	 */
	public function __call($methodName, array $arguments) {
		if (substr($methodName, 0, 5) === 'build' && strlen($methodName) > 6) {
			$objectName = strtolower(substr($methodName, 5, 1)) . substr($methodName, 6);
			$overrideProperties = isset($arguments[0]) ? $arguments[0] : array();
			return $this->buildObject($objectName, $overrideProperties);
		} elseif (substr($methodName, 0, 6) === 'create' && strlen($methodName) > 7) {
			$objectName = strtolower(substr($methodName, 6, 1)) . substr($methodName, 7);
			$overrideProperties = isset($arguments[0]) ? $arguments[0] : array();
			return $this->findOrCreate($objectName, $overrideProperties);
		}
		trigger_error('Call to undefined method ' . get_class($this) . '::' . $methodName, E_USER_ERROR);
	}

	/**
	 * Overwrite to implement own property definitions
	 *
	 * @param object $object
	 * @param array $properties
	 */
	protected function setCustomProperties($object, $properties) {
	}

	/**
	 * Reset this fixture factory
	 *
	 * Implement in custom factories to reset instance caches.
	 *
	 * @return void
	 */
	public function reset() {
		$this->instances = array();
	}

	/**
	 * @param mixed $objectIdentification
	 * @return object
	 * @throws \InvalidArgumentException
	 */
	public function get($objectIdentification) {
		if ($objectIdentification instanceof $this->baseType) {
			$object = $objectIdentification;
		} elseif (is_array($objectIdentification)) {
			$object = $this->findOrCreate($this->getDefaultObjectName(), $objectIdentification);
		} elseif (is_numeric($objectIdentification)) {
			$object = $this->findOrCreate($this->getDefaultObjectName(), array('id' => $objectIdentification));
		} elseif (is_string($objectIdentification)) {
			$object = $this->findOrCreate($objectIdentification);
		} else {
			throw new \InvalidArgumentException('Expected object, fixture name, id or properties array got ' . gettype($objectIdentification));
		}

		return $object;
	}

	/**
	 * @param string $objectName
	 * @param $overrideProperties
	 */
	public function findOrCreate($objectName, $overrideProperties = array()) {
		$object = NULL;
		if (isset($overrideProperties[$this->identifierPropertyName])) {
			$object = $this->getExistingObject($overrideProperties[$this->identifierPropertyName]);
		} elseif (isset($this->fixtureDefinitions[$objectName][$this->identifierPropertyName])) {
			$object = $this->getExistingObject($this->fixtureDefinitions[$objectName][$this->identifierPropertyName]);
		}

		if ($object !== NULL) {
			return $object;
		}

		return $this->createObject($objectName, $overrideProperties);
	}

	/**
	 * @param string $objectIdentifier
	 * @return object
	 */
	protected function getExistingObject($objectIdentifier) {
		$cacheIdentifier = $this->createCacheIdentifier($objectIdentifier);

		if (isset($this->instances[$cacheIdentifier])) {
			return $this->instances[$cacheIdentifier];
		}

		return $this->persistenceManager->getObjectByIdentifier($objectIdentifier, $this->baseType);
	}

	/**
	 * @param string $objectIdentifier
	 * @return string
	 */
	protected function createCacheIdentifier($objectIdentifier) {
		return md5($this->baseType . $objectIdentifier);
	}

	/**
	 * @return string
	 */
	protected function getDefaultObjectName() {
		if ($this->fixtureDefinitions === array()) {
			throw new \Exception('No fixture definitions in ' . gettype($this));
		}
		$objectNames = array_keys($this->fixtureDefinitions);
		$defaultObjectName = $objectNames[0];
		return $defaultObjectName;
	}
}
