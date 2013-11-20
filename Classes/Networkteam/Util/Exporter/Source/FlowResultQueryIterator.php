<?php

namespace Networkteam\Util\Exporter\Source;
use TYPO3\Flow\Persistence\QueryResultInterface;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

class FlowResultQueryIterator implements \Exporter\Source\SourceIteratorInterface {

	/**
	 * @var \TYPO3\Flow\Persistence\QueryResultInterface
	 */
	protected $result;

	/**
	 * @var string
	 */
	protected $dateTimeFormat;

	/**
	 * @var array
	 */
	protected $propertyPaths;

	/**
	 * @param \TYPO3\Flow\Persistence\QueryResultInterface $result
	 * @param array $fields
	 * @param string $dateTimeFormat
	 */
	public function __construct(QueryResultInterface $result, array $fields, $dateTimeFormat = 'r') {
		$this->result = $result;

		$this->propertyPaths = array();
		foreach ($fields as $name => $field) {
			if (is_string($name) && is_string($field)) {
				$this->propertyPaths[$name] = $field;
			} else {
				$this->propertyPaths[$field] = $field;
			}
		}

		$this->dateTimeFormat = $dateTimeFormat;
	}


	/**
	 * {@inheritdoc}
	 */
	public function current()
	{
		$current = $this->result->current();

		$data = array();

		foreach($this->propertyPaths as $fieldName => $propertyPath) {
			$data[$fieldName] = $this->getValue(\TYPO3\Flow\Reflection\ObjectAccess::getProperty($current, $propertyPath));
		}

		return $data;
	}

	/**
	 * @param $value
	 *
	 * @return null|string
	 */
	protected function getValue($value)
	{
		if (is_array($value) or $value instanceof \Traversable) {
			$value = null;
		} elseif ($value instanceof \DateTime) {
			$value = $value->format($this->dateTimeFormat);
		} elseif (is_object($value)) {
			$value = (string) $value;
		}

		return $value;
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Move forward to next element
	 * @link http://php.net/manual/en/iterator.next.php
	 * @return void Any returned value is ignored.
	 */
	public function next() {
		return $this->result->next();
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Return the key of the current element
	 * @link http://php.net/manual/en/iterator.key.php
	 * @return mixed scalar on success, or null on failure.
	 */
	public function key() {
	return $this->result->key();
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Checks if current position is valid
	 * @link http://php.net/manual/en/iterator.valid.php
	 * @return boolean The return value will be casted to boolean and then evaluated.
	 * Returns true on success or false on failure.
	 */
	public function valid() {
		return $this->result->valid();
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Rewind the Iterator to the first element
	 * @link http://php.net/manual/en/iterator.rewind.php
	 * @return void Any returned value is ignored.
	 */
	public function rewind() {
		$this->result->rewind();
	}}
?>