<?php
namespace Networkteam\Util\Command;

/***************************************************************
 *  (c) 2017 networkteam GmbH - all rights reserved
 ***************************************************************/

use Neos\Flow\Annotations as Flow;
use Neos\Cache\Backend\SimpleFileBackend;
use Neos\Cache\Backend\TransientMemoryBackend;
use Neos\Cache\Backend\NullBackend;

class CacheCommandController extends \Neos\Flow\Cli\CommandController
{

	/**
	 * @var \Neos\Flow\Cache\CacheManager
	 * @Flow\Inject
	 */
	protected $cacheManager;

	/**
	 * @var \Neos\Flow\Configuration\ConfigurationManager
	 * @Flow\Inject
	 */
	protected $configurationManager;

	/**
	 * Clears all non file caches
	 */
	public function clearNonFileCachesCommand(): void
	{
		$caches = $this->cacheManager->getCacheConfigurations();
		foreach ($caches as $name => $configuration) {
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

	/**
	 * Refresh configuration cache (flush and load again)
	 */
	public function refreshConfigCommand(): void
	{
		try {
			$this->configurationManager->refreshConfiguration();
			$this->outputLine('Configuration has been flushed and loaded again.');
		} catch (\Exception $e) {
			$this->outputLine(sprintf('Failed: %s', $e->getMessage()));
		}
	}
}
