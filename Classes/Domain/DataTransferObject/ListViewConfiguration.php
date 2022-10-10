<?php

namespace Networkteam\Util\Domain\DataTransferObject;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

class ListViewConfiguration
{
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


    const DIRECTION_ASCENDING = 'ASC';

    const DIRECTION_DESCENDING = 'DESC';

    /**
     * Array where keys represent property name and values the sort direction
     *
     * Example for array:
     *
     *   [
     *     'myProperty' => 'ASC',
     *     'anotherProperty' => 'DESC'
     *     '*' => 'ASC'
     *   ]
     *
     * @var array
     */
    protected array $sortDirections = [];

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
    protected array $filter = [];

    /**
     * defines the properties which can be filtered
     * used to build the filter form
     *
     * @var array
     */
    protected array $filterableProperties = [];

    /**
     * @var int
     */
    protected int $itemsPerPage = 20;

    /**
     * Logical type to connect all constraints with in the query
     *
     * @var int
     */
    protected int $logicalType = self::LOGICAL_TYPE_AND;

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

    public function setSortDirection(string $propertyName, string $sortDirection = self::DIRECTION_ASCENDING): void
    {
        if (in_array($sortDirection, [self::DIRECTION_ASCENDING, self::DIRECTION_DESCENDING])) {
            $this->sortDirections[$propertyName] = $sortDirection;
        }
    }

    public function setSortDirections(array $sortDirections): void
    {
		foreach ($sortDirections as $propertyName => $direction) {
			$this->setSortDirection($propertyName, $direction);
		}
    }

    public function getSortDirections(): array
    {
        return $this->sortDirections;
    }

    public function getSortDirection(string $propertyName = '*'): ?string
    {
        if (isset($this->sortDirections[$propertyName])) {
            return $this->sortDirections[$propertyName];
        }

        return null;
    }

    public function getSortProperties(): array
    {
        return array_keys($this->sortDirections);
    }

    /**
     * Resets logical type to LOGICAL_TYPE_AND
     */
    public function resetLogicalType(): void
    {
        $this->logicalType = self::LOGICAL_TYPE_AND;
    }


    public function hasSorting(): bool
    {
        return count($this->sortDirections) > 0;
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
            if (strpos($propertyName, '_') !== false) {
                unset($filter[$propertyName]);
                $propertyName = strtr($propertyName, '_', '.');
                $filter[$propertyName] = $propertyValue;
            }
        }
        return $filter;
    }

    /**
	 * Replaces "_" by "." in property names
     */
    public function getSortDirectionsWithPropertyPath(): array
    {
        $sortDirections = [];
        foreach ($this->sortDirections as $propertyName => $sortDirection) {
            $propertyPath = strtr($propertyName, '_', '.');
            $sortDirections[$propertyPath] = $sortDirection;
        }

        return $sortDirections;
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

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'sortDirections' => $this->sortDirections,
            'itemsPerPage' => $this->itemsPerPage,
            'filter' => $this->filter,
            'filterableProperties' => $this->filterableProperties,
            'logicalType' => $this->logicalType
        ];
    }
}
