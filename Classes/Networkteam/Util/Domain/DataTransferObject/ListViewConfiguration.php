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
	 * Also accepts array
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
	protected $filter = [];

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
	 */
	public function setFilter(array $filter): void
    {
		foreach ($filter as $name => $configuration) {
			if (!$this->isValidFilterConfiguration($configuration)) {
				unset($filter[$name]);
			}
		}
		$this->filter = $filter;
	}

	public function getFilter(): array
    {
		return $this->filter;
	}

	public function hasFilter(string $name): bool
    {
		return isset($this->filter[$name]);
	}

	public function setItemsPerPage(int $amount): void
    {
		$this->itemsPerPage = $amount;
	}

	public function getItemsPerPage(): int
    {
		return $this->itemsPerPage;
	}

	public function setLogicalType(int $type): void
    {
		$this->logicalType = (int)$type;
	}

	public function getLogicalType(): int
    {
		return $this->logicalType;
	}

	public function setSortDirection(string $sortDirection): void
    {
		if (in_array($sortDirection, array('ASC', 'DESC'))) {
			$this->sortDirection = $sortDirection;
		} else {
			$this->sortDirection = 'ASC';
		}
	}

	public function getSortDirection(): string
    {
		return $this->sortDirection;
	}

	public function setSortProperty(string $sortField): void
    {
		$this->sortProperty = $sortField;
	}

	/**
	 * Resets logical type to LOGICAL_TYPE_AND
	 */
	public function resetLogicalType(): void
    {
		$this->logicalType = self::LOGICAL_TYPE_AND;
	}

	public function getSortProperty(): string
    {
		return $this->sortProperty;
	}

	public function hasSorting(): bool
    {
		return is_array($this->sortProperty) ? (count($this->sortProperty) > 0) : ((string)$this->sortProperty !== '');
	}

	public function setFilterableProperties(array $filterableProperties): void
    {
		$this->filterableProperties = $filterableProperties;
	}

	public function getFilterableProperties(): array
    {
		return $this->filterableProperties;
	}

	public function getConfiguredFilters(): array
    {
		$configuredFilters = [];
		foreach ($this->filterableProperties as $name => $configuration) {
			$filter = [
				'name' => $name,
				'operand' => '',
				'operator' => 'like'
			];
			if (isset($this->filter[$name])) {
				$filter['operand'] = $this->filter[$name]['operand'];
				$filter['operator'] = $this->filter[$name]['operator'];
			}
			$configuredFilters[] = $filter;
		}

		return $configuredFilters;
	}

	public function addFilter(string $name, array $configuration): void
    {
		if ($this->isValidFilterConfiguration($configuration)) {
			$this->filter[$name] = $configuration;
		}
	}

	/**
	 * This method will convert underscores in filter properties to dots for property paths
	 * to use the property names in repositories.
	 */
	public function getFilterWithPropertyPaths(): array
    {
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

	public function getSortPropertyWithPropertyPath(): string
    {
		return strtr($this->sortProperty, '_', '.');
	}

	/**
	 * Checks if given filter configuration array is valid
	 */
	protected function isValidFilterConfiguration(array $configuration): bool
    {
		return (isset($configuration['operator']) && (string)$configuration['operator'] !== '' && isset($configuration['operand']));
	}

	public function removeFilter(string $filterName): void
    {
		unset($this->filter[$filterName]);
	}
}
