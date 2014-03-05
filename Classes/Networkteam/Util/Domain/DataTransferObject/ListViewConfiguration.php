<?php
namespace Networkteam\Util\Domain\DataTransferObject;
/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

class ListViewConfiguration {

	/**
	 * All filters set will be connected by OR
	 */
	const LOGICAL_TYPE_OR = 1;

	/**
	 * All filters set will be connected by AND (default)
	 */
	const LOGICAL_TYPE_AND = 2;

	/**
	 * All filters set will be connected by NOT
	 */
	const LOGICAL_TYPE_NOT = 3;

	/**
	 * @var string
	 */
	protected $sortDirection = 'ASC';

	/**
	 * @var string
	 */
	protected $sortProperty;

	/**
	 * array(
	 *   'name' => array(
	 *     'operator' => 'equals',
	 *     'operand' => 'Foo'
	 *   ),
	 *   'lastUpdate' => array(
	 *     'operator' => 'greaterThan',
	 *     'operand' => '134234423'
	 *   )
	 * )
	 *
	 * @var array
	 */
	protected $filter = array();

	/**
	 * defines the properties which can be filtered
	 * used to build the filter form
	 *
	 * @var array
	 */
	protected $filterableProperties = array();

	/**
	 * @var integer
	 */
	protected $itemsPerPage = 20;

	/**
	 * Logical type to connect all contraints with in the query
	 *
	 * @var integer
	 */
	protected $logicalType = self::LOGICAL_TYPE_AND;

	/**
	 * Validate and set the filter configuration
	 *
	 * @param array $filter
	 */
	public function setFilter($filter) {
		foreach ($filter as $name => $configuration) {
			if (!$this->isValidFilterConfiguration($configuration)) {
				unset($filter[$name]);
			}
		}
		$this->filter = $filter;
	}

	/**
	 * @return array
	 */
	public function getFilter() {
		return $this->filter;
	}

	/**
	 * @param string $name
	 * @return boolean
	 */
	public function hasFilter($name) {
		return isset($this->filter[$name]);
	}

	/**
	 * @param integer $amount
	 */
	public function setItemsPerPage($amount) {
		$this->itemsPerPage = $amount;
	}

	/**
	 * @return integer
	 */
	public function getItemsPerPage() {
		return $this->itemsPerPage;
	}

	/**
	 * @param integer $type
	 */
	public function setLogicalType($type) {
		$this->logicalType = (int)$type;
	}

	/**
	 * @return integer
	 */
	public function getLogicalType() {
		return $this->logicalType;
	}

	/**
	 * @param string $sortDirection
	 */
	public function setSortDirection($sortDirection) {
		if (in_array($sortDirection, array('ASC', 'DESC'))) {
			$this->sortDirection = $sortDirection;
		} else {
			$this->sortDirection = 'ASC';
		}
	}

	/**
	 * @return string
	 */
	public function getSortDirection() {
		return $this->sortDirection;
	}

	/**
	 * @param string $sortField
	 */
	public function setSortProperty($sortField) {
		$this->sortProperty = $sortField;
	}

	/**
	 * Resets logical type to LOGICAL_TYPE_AND
	 */
	public function resetLogicalType() {
		$this->logicalType = self::LOGICAL_TYPE_AND;
	}

	/**
	 * @return string
	 */
	public function getSortProperty() {
		return $this->sortProperty;
	}

	/**
	 * @return boolean
	 */
	public function hasSorting() {
		return (string)$this->sortProperty !== '';
	}

	/**
	 * @param array $filterableProperties
	 */
	public function setFilterableProperties($filterableProperties) {
		$this->filterableProperties = $filterableProperties;
	}

	/**
	 * @return array
	 */
	public function getFilterableProperties() {
		return $this->filterableProperties;
	}

	/**
	 * @return array
	 */
	public function getConfiguredFilters() {
		$configuredFilters = array();
		foreach ($this->filterableProperties as $name => $configuration) {
			$filter = array(
				'name' => $name,
				'operand' => '',
				'operator' => 'like'
			);
			if (isset($this->filter[$name])) {
				$filter['operand'] = $this->filter[$name]['operand'];
				$filter['operator'] = $this->filter[$name]['operator'];
			}
			$configuredFilters[] = $filter;
		}

		return $configuredFilters;
	}

	/**
	 * @param string $name
	 * @param array $configuration
	 * @return void
	 */
	public function addFilter($name, $configuration) {
		if ($this->isValidFilterConfiguration($configuration)) {
			$this->filter[$name] = $configuration;
		}
	}

	/**
	 * This method will convert underscores in filter properties to dots for property paths
	 * to use the property names in repositories.
	 *
	 * @return array
	 */
	public function getFilterWithPropertyPaths() {
		$filter = $this->filter;
		foreach ($filter as $propertyName => $propertyValue) {
			if (strpos($propertyName, '_') !== FALSE) {
				unset($filter[$propertyName]);
				$propertyName = strtr($propertyName, '_', '.');
				$filter[$propertyName] = $propertyValue;
			}
		}
		return $filter;
	}

	/**
	 * @return string
	 */
	public function getSortPropertyWithPropertyPath() {
		return strtr($this->sortProperty, '_', '.');
	}

	/**
	 * Checks if given filter configuration array is valid
	 *
	 * @param array $configuration
	 * @return boolean
	 */
	protected function isValidFilterConfiguration(array $configuration) {
		return (isset($configuration['operator']) && (string)$configuration['operator'] !== '' && isset($configuration['operand']));
	}

	/**
	 * Deletes a filter
	 *
	 * @param $filterName
	 */
	public function removeFilter($filterName) {
		unset($this->filter[$filterName]);
	}
}

?>