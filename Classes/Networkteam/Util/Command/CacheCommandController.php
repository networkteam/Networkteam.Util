<?php
namespace Networkteam\Util\Command;

/***************************************************************
 *  (c) 2017 networkteam GmbH - all rights reserved
 ***************************************************************/

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Cache\Backend\SimpleFileBackend;
use TYPO3\Flow\Cache\Backend\TransientMemoryBackend;
use TYPO3\Flow\Cache\Backend\NullBackend;

class CacheCommandController extends \TYPO3\Flow\Cli\CommandController {

	/**
	 * @var \TYPO3\Flow\Cache\CacheManager
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
