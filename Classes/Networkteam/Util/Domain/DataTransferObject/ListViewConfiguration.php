<?php
namespace Networkteam\Util\Domain\DataTransferObject;
/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

class ListViewConfiguration {

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
	 * @var int
	 */
	protected $itemsPerPage = 20;

	/**
	 * Set the filter configuration
	 *
	 * @param array $filter
	 */
	public function setFilter($filter) {
		$this->filter = $filter;
	}

	/**
	 * @return array
	 */
	public function getFilter() {
		return $this->filter;
	}

	/**
	 * @param int $amount
	 */
	public function setItemsPerPage($amount) {
		$this->itemsPerPage = $amount;
	}

	/**
	 * @return int
	 */
	public function getItemsPerPage() {
		return $this->itemsPerPage;
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
		$this->filter[$name] = $configuration;
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
}

?>