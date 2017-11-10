<?php
namespace Networkteam\Util\Command;

/***************************************************************
 *  (c) 2017 networkteam GmbH - all rights reserved
 ***************************************************************/

use Neos\Flow\Annotations as Flow;
use Neos\Cache\Backend\SimpleFileBackend;
use Neos\Cache\Backend\TransientMemoryBackend;
use Neos\Cache\Backend\NullBackend;

class CacheCommandController extends \Neos\Flow\Cli\CommandController {

	/**
	 * @var \Neos\Flow\Cache\CacheManager
	 * @Flow\Inject
	 */
	protected $cacheManager;

	/**
	 * Clears all non file caches
	 */
	public function clearNonFileCachesCommand() {
		$caches = $this->cacheManager->getCacheConfigurations();
		foreach($caches as $name => $configuration) {
			$cache = $this->cacheManager->getCache($name);
			$backend = $cache->getBackend();
			if (!$backend instanceof SimpleFileBackend &&
				!$backend instanceof NullBackend &&
				!$backend instanceof TransientMemoryBackend
			) {
				$cache->flush();
				$this->outputLine(sprintf('Flushed cache %s', $name));
			}
		}
	}
}
