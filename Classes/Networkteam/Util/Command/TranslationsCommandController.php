<?php
namespace Networkteam\Util\Command;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

use Networkteam\Util\Translation\TranslationStripper;
use Networkteam\Util\Translation\XliffFileUpdater;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use TYPO3\Fluid\Core\Parser\TemplateParser;
use TYPO3\Flow\Annotations as Flow;

class TranslationsCommandController extends \TYPO3\Flow\Cli\CommandController {

	/**
	 * @var \Networkteam\Util\Translation\TranslationStripper
	 * @Flow\Inject
	 */
	protected $translationStripper;

	/**
	 * @var \Networkteam\Util\Translation\XliffFileUpdater
	 * @Flow\Inject
	 */
	protected $xliffFileUpdater;

	/**
	 * Update XliffFile
	 * @param string $packageKey
	 * @param string $locale
	 * @param string $catalog
	 */
	public function updateXliffFileCommand($packageKey, $locale = 'en', $catalog = 'Main') {
		$translationUpdates = $this->translationStripper->stripIds($packageKey);

		$this->xliffFileUpdater->updateCatalogue($translationUpdates, $packageKey, $locale, $catalog);

		echo "ids gesamt: " . count($translationUpdates);
	}

	/**
	 * List translation IDs of package
	 *
	 * @param string $packageKey
	 * @param bool $missingOnly
	 */
	public function listIdsCommand($packageKey, $missingOnly = FALSE) {
		$translationUpdates = $this->translationStripper->stripIds($packageKey);
		uasort($translationUpdates, function($a, $b) {
			if($a == $b) {
				return 0;
			}
			return ($a < $b) ? -1 : 1;
		});
		foreach ($translationUpdates as $transNode) {
			if($missingOnly === FALSE) {
				echo $transNode['id'] . "\n";
			} else {
				if($transNode['text'] === '') {
					echo $transNode['id'] . "\n";
				}
			}
		}
		echo "ids gesamt: " . count($translationUpdates) . "\n";;
	}
}

?>