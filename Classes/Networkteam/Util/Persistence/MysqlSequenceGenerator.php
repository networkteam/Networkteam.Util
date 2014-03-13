<?php
namespace Networkteam\Util\Persistence;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

use Doctrine\ORM\EntityManager;
use TYPO3\Flow\Annotations as Flow;

class MysqlSequenceGenerator implements SequenceGeneratorInterface {

	/**
	 * @var \Doctrine\Common\Persistence\ObjectManager
	 * @Flow\Inject
	 */
	protected $entityManager;

	/**
	 * @return int
	 */
	public function next($sequenceName) {
		/* @var $connection \Doctrine\DBAL\Connection */
		$connection = $this->entityManager->getConnection();
		$connection->insert('networkteam_sequence', array('sequencename' => $sequenceName), array('string'));
		return $connection->lastInsertId();
	}
}
