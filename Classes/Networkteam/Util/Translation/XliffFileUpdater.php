<?php
namespace Networkteam\Util\Translation;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

use Neos\Flow\Annotations as Flow;

class XliffFileUpdater {

	/**
	 * @var \Symfony\Component\Translation\Loader\XliffFileLoader
	 */
	protected $xliffFileLoader;

	/**
	 * @var \Networkteam\Util\Translation\XliffFileDumper
	 * @Flow\Inject
	 */
	protected $xliffFileDumper;

	/**
	 * @var \Neos\Flow\Package\PackageManager
	 * @Flow\Inject
	 */
	protected $packageManager;

	public function initializeObject() {
		$this->xliffFileLoader = new \Symfony\Component\Translation\Loader\XliffFileLoader();
	}

	/**
	 * @param string $packageKey
	 * @param string $locale
	 * @param string $catalogue
	 */
	public function updateCatalogue($translationUpdates, $packageKey, $locale, $catalogue = 'Main') {
		$resource = $this->getTranslationResourcePath($packageKey, $locale, $catalogue);
		$existingCatalogue = $this->xliffFileLoader->load($resource, $locale);
		foreach ($translationUpdates as $translation) {
			if (!$existingCatalogue->has($translation['id'])) {
				$existingCatalogue->set($translation['id'], $translation['text']);
			}
		}

		$this->xliffFileDumper->dump($existingCatalogue, array('path' => '/tmp/'));
		copy('/tmp/' . 'messages.' . $locale . '.xlf', $resource);
	}

	/**
	 * @param $packageKey
	 * @param $locale
	 * @param $catalogue
	 *
	 * @return string
	 */
	protected function getTranslationResourcePath($packageKey, $locale, $catalogue) {
		$package = $this->packageManager->getPackage($packageKey);
		$resourcesPath = $package->getResourcesPath();
		$translationResource = $resourcesPath . 'Private/Translations/' . $locale . '/' . $catalogue . '.xlf';

		return $translationResource;
	}
}
