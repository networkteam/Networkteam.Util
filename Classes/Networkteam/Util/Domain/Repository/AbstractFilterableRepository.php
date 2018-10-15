<?php
namespace Networkteam\Util\Domain\Repository;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

use Networkteam\Util\Domain\DataTransferObject\ListViewConfiguration;
use Neos\Flow\Persistence\QueryInterface;

abstract class AbstractFilterableRepository extends \Neos\Flow\Persistence\Doctrine\Repository {

	/**
	 * @var array
	 */
	protected $filterableProperties = array();

	/**
	 * @param \Networkteam\Util\Domain\DataTransferObject\ListViewConfiguration $listViewConfiguration
	 * @return \Neos\Flow\Persistence\QueryResultInterface
	 */
	public function findByListViewConfiguration(ListViewConfiguration $listViewConfiguration) {
		$query = $this->createQuery();

		$filter = $listViewConfiguration->getFilterWithPropertyPaths();
		$constraint = $this->buildFiltersConstraint($filter, $query, $listViewConfiguration->getLogicalType());
		if ($constraint !== NULL) {
			$query->matching($constraint);
		}

		if ($listViewConfiguration->hasSorting()) {
			$query->setOrderings($this->getOrderings($listViewConfiguration));
		}

		return $query->execute();
	}

	/**
	 * @param array $filters
	 * @param \Neos\Flow\Persistence\QueryInterface $query
	 * @param integer $logicalTypeValue
	 * @return object A constraint for the filters or NULL if no filters apply
	 */
	protected function buildFiltersConstraint(array $filters, $query, $logicalTypeValue = ListViewConfiguration::LOGICAL_TYPE_AND) {
		$constraints = array();
		foreach ($filters as $propertyName => $filter) {
			$constraint = $this->getConstraintForFilter($filter, $propertyName, $query);
			if ($constraint !== NULL) {
				$constraints[] = $constraint;
			}
		}

		if (count($constraints) > 0) {
			$logicalTypeName = $this->getLogicalTypeName($logicalTypeValue);
			return $query->$logicalTypeName($constraints);
		} else {
			return NULL;
		}
	}

	/**
	 * Returns name of logical function to combine constraints with
	 *
	 * @param integer $logicalTypeValue
	 * @return string
	 */
	public function getLogicalTypeName($logicalTypeValue) {
		$typeName = 'logicalAnd';

		if ($logicalTypeValue === ListViewConfiguration::LOGICAL_TYPE_OR) {
			$typeName = 'logicalOr';
		} elseif ($logicalTypeValue === ListViewConfiguration::LOGICAL_TYPE_NOT) {
			$typeName = 'logicalNot';
		}

		return $typeName;
	}

	/**
	 * @param array $filter
	 * @param string $propertyName
	 * @param \Neos\Flow\Persistence\QueryInterface $query
	 * @return object
	 * @throws \InvalidArgumentException
	 */
	protected function getConstraintForFilter(array $filter, $propertyName, QueryInterface $query) {
		$constraint = NULL;
		if (!isset($this->filterableProperties[$propertyName])) {
			throw new \InvalidArgumentException('Filter for property "' . $propertyName . '" not supported', 1369912305);
		}
		if (!isset($filter['operator'])) {
			throw new \InvalidArgumentException('No operator for property "' . $propertyName . '"', 1369993785);
		}
		switch ($filter['operator']) {
			case 'equals':
				if ($filter['operand'] !== '') {
					$constraint = $query->equals($propertyName, $filter['operand']);
				}
				break;
			case 'like':
				if ((string)$filter['operand'] !== '') {
					$constraint = $query->like($propertyName, '%' . $filter['operand'] . '%');
				}
				break;
			case 'startsWith':
				if ((string)$filter['operand'] !== '') {
					$constraint = $query->like($propertyName, $filter['operand'] . '%');
				}
				break;
			case 'endsWith':
				if ((string)$filter['operand'] !== '') {
					$constraint = $query->like($propertyName, '%' . $filter['operand']);
				}
				break;
			case 'is':
				if ((string)$filter['operand'] === 'NULL') {
					$constraint = $query->equals($propertyName, NULL);
				}
				break;
			case 'in':
				if (is_array($filter['operand'])) {
					$constraint = $query->in($propertyName, implode(',', $filter['operand']));
				} else {
					$constraint = $query->in($propertyName, (string)$filter['operand']);
				}
				break;
			case 'contains':
				if ((string)$filter['operand'] !== '') {
					$constraint = $query->contains($propertyName, $filter['operand']);
				}
				break;
			case 'greaterThanOrEqual':
				if ((string)$filter['operand'] !== '') {
					// handle value: "Type|Value"
					if (strpos($filter['operand'], '|') !== FALSE) {
						list($type, $value) = explode('|', $filter['operand']);
						switch ($type) {
							case 'DateTime':
								$typeAwareValue = new \DateTime($value);
								break;
							default:
								throw new \InvalidArgumentException('Unknown type "' . $type . '"', 1394030370);
						}
						$constraint = $query->greaterThanOrEqual($propertyName, $typeAwareValue);

					} else {
						$constraint = $query->greaterThanOrEqual($propertyName, $filter['operand']);
					}
				}
				break;

			default:
				throw new \InvalidArgumentException('Unknown operator "' . $filter['operator'] . '", allowed operators are "equals,like,is,contains,greaterThanOrEqual"', 1369911739);
		}

		return $constraint;
	}

	/**
	 * @param ListViewConfiguration $listViewConfiguration
	 * @return array
	 * @throws \InvalidArgumentException
	 */
	protected function getOrderings(ListViewConfiguration $listViewConfiguration) {
		$sortProperties = array();
		$isSingleSortDirection = TRUE;
		$sortDirections = array();
		if (is_array($listViewConfiguration->getSortProperty())) {
			if (is_array($listViewConfiguration->getSortDirection())) {
				if (count($listViewConfiguration->getSortProperty()) !== count($listViewConfiguration->getSortDirection())) {
					throw new \InvalidArgumentException('The count of properties does not match the count of directions, these must match or give the direction as string, then all properties get the same sort direction', 1408113192);
				}
				$sortDirections = $listViewConfiguration->getSortDirection();
				$isSingleSortDirection = FALSE;
			}

			$rawSortProperties = $listViewConfiguration->getSortProperty();
			foreach ($rawSortProperties as $index => $sortProperty) {
				$sortProperty = strtr($sortProperty, '_', '.');
				$sortProperties[$sortProperty] = $isSingleSortDirection ? $listViewConfiguration->getSortDirection() : $sortDirections[$index];
			}
		} else {
			if (is_array($listViewConfiguration->getSortDirection())) {
				throw new \InvalidArgumentException('Sort directions mut not be an array but a string', 1408539136);
			}
			$sortProperties = array($listViewConfiguration->getSortPropertyWithPropertyPath() => $listViewConfiguration->getSortDirection());
		}

		return $sortProperties;
	}
}
