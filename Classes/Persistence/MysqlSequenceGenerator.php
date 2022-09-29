<?php
namespace Networkteam\Util\Persistence;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

use Doctrine\ORM\EntityManager;
use Neos\Flow\Annotations as Flow;

class MysqlSequenceGenerator implements SequenceGeneratorInterface {

	/**
	 * @var \Doctrine\ORM\EntityManagerInterface
	 * @Flow\Inject
	 */
	protected $entityManager;

	public function next($sequenceName): int
    {
		/* @var $connection \Doctrine\DBAL\Connection */
		$connection = $this->entityManager->getConnection();
		$connection->insert('networkteam_sequence', array('sequencename' => $sequenceName), array('string'));
		return $connection->lastInsertId();
	}
}
