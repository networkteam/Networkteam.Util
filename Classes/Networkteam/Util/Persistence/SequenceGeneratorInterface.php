<?php
namespace Networkteam\Util\Persistence;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

interface SequenceGeneratorInterface {

	public function next($sequenceName): int;

}
