<?php
namespace Networkteam\Util\Domain\Repository;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

use Networkteam\Util\Domain\DataTransferObject\ListViewConfiguration;
use TYPO3\Flow\Persistence\QueryInterface;

abstract class AbstractFilterableRepository extends \TYPO3\Flow\Persistence\Doctrine\Repository {

	/**
	 * @var array
	 */
	protected $filterableProperties = array();

	/**
	 * @param \Networkteam\Util\Domain\DataTransferObject\ListViewConfiguration $listViewConfiguration
	 * @return \TYPO3\Flow\Persistence\QueryResultInterface
	 */
	public function findByListViewConfiguration(ListViewConfiguration $listViewConfiguration) {
		$query = $this->createQuery();

		$filter = $listViewConfiguration->getFilterWithPropertyPaths();

		$constraint = $this->buildFiltersConstraint($filter, $query);
		if ($constraint !== NULL) {
			$query->matching($constraint);
		}

		if ($listViewConfiguration->hasSorting()) {
			$query->setOrderings(array($listViewConfiguration->getSortPropertyWithPropertyPath() => $listViewConfiguration->getSortDirection()));
		}

		return $query->execute();
	}

	/**
	 * @param array $filters
	 * @param \TYPO3\Flow\Persistence\QueryInterface $query
	 * @return object A constraint for the filters or NULL if no filters apply
	 */
	protected function buildFiltersConstraint($filters, $query) {
		$constraints = array();
		foreach ($filters as $propertyName => $filter) {
			$constraint = $this->getConstraintForFilter($filter, $propertyName, $query);
			if ($constraint !== NULL) {
				$constraints[] = $constraint;
			}
		}
		if (count($constraints) > 0) {
			return $query->logicalAnd($constraints);
		} else {
			return NULL;
		}
	}

	/**
	 * @param array $filter
	 * @param string $propertyName
	 * @param \TYPO3\Flow\Persistence\QueryInterface $query
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
				if ((string)$filter['operand'] !== '') {
					$constraint = $query->equals($propertyName, $filter['operand']);
				}
				break;
			case 'like':
				if ((string)$filter['operand'] !== '') {
					$constraint = $query->like($propertyName, '%' . $filter['operand'] . '%');
				}
				break;
			case 'is':
				if ((string)$filter['operand'] === 'NULL') {
					$constraint = $query->equals($propertyName, NULL);
				}
				break;
			case 'contains':
				if ((string) $filter['operand'] !== '') {
					$constraint = $query->contains($propertyName, $filter['operand']);
				}
				break;
			default:
				throw new \InvalidArgumentException('Unknown operator "' . $filter['operator'] . '"', 1369911739);
		}

		return $constraint;
	}

}
?>