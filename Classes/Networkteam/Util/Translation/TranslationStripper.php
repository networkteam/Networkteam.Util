<?php
namespace Networkteam\Util\Translation;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

use Symfony\Component\Finder\Finder;
use Neos\Flow\Annotations as Flow;
use Neos\FluidAdaptor\Core\Parser\ParsingState;
use Neos\FluidAdaptor\Core\Parser\SyntaxTree\ObjectAccessorNode;
use Neos\FluidAdaptor\Core\Parser\SyntaxTree\RootNode;
use Neos\FluidAdaptor\Core\Parser\SyntaxTree\TextNode;

class TranslationStripper {
	/**
	 * Pattern to be resolved for "@templateRoot" in the other patterns.
	 *
	 * @var string
	 */
	protected $emberTemplateRootPathPattern = '@packageResourcesPath/Private/Ember';

	/**
	 * Pattern to be resolved for "@templateRoot" in the other patterns.
	 *
	 * @var string
	 */
	protected $templateRootPathPattern = '@packageResourcesPath/Private/Templates';

	/**
	 * Pattern to be resolved for "@partialRoot" in the other patterns.
	 *
	 * @var string
	 */
	protected $partialRootPathPattern = '@packageResourcesPath/Private/Partials';

	/**
	 * Pattern to be resolved for "@layoutRoot" in the other patterns.
	 *
	 * @var string
	 */
	protected $layoutRootPathPattern = '@packageResourcesPath/Private/Layouts';

	/**
	 * @var array
	 */
	protected $translationNodes = array();

	/**
	 * @var \Neos\Flow\Package\PackageManager
	 * @Flow\Inject
	 */
	protected $packageManager;

	/**
	 * @var \Neos\FluidAdaptor\Core\Parser\TemplateParser
	 * @Flow\Inject
	 */
	protected $templateParser;

	/**
	 * @param string $packageKey
	 */
	public function stripIds($packageKey) {
		$fileSet = $this->findFileSet($packageKey);
		$translationNodes = array();
		/** @var $file SplFileInfo */
		foreach ($fileSet as $file) {
			$parsingState = $this->templateParser->parse($file->getContents());
			$this->findTranslationNodes($parsingState);
		}

		$translationUpdates = array();
		foreach ($this->translationNodes as $transNode) {
			/** @var $transNode \Neos\FluidAdaptor\Core\Parser\SyntaxTree\ViewHelperNode */
			$arguments = $transNode->getArguments();
			if (isset($arguments['id']) && ($arguments['id'] instanceof TextNode || $arguments['id'] instanceof ObjectAccessorNode)) {
				$idValue = $arguments['id'] instanceof ObjectAccessorNode ? $arguments['id']->getObjectPath() : $arguments['id']->getText();
					// don't parse existing nodes again
				if (isset($translationUpdates[$idValue])) {
					continue;
				}
				$trans = array(
					'id' => $idValue
				);
				$trans['text'] = $this->getTranslationText($arguments);
				$translationUpdates[$trans['id']] = $trans;
			} elseif (isset($arguments['id']) && $arguments['id'] instanceof RootNode) {
				$nodes = $arguments['id']->getChildNodes();
					// In this case we have a label expanded with an object property
				if (count($nodes) === 2 && $nodes[0] instanceof TextNode && $nodes[1] instanceof ObjectAccessorNode) {
					$idValue = $nodes[0]->getText();
					if (isset($translationUpdates[$idValue])) {
						continue;
					}
					$trans = array(
						'id' => $idValue
					);
					$trans['text'] = $nodes[1]->getObjectPath();
					$translationUpdates[$trans['id']] = $trans;
				}
			}
		}

		return $translationUpdates;
	}

	/**
	 * @param $arguments
	 * @param $trans
	 * @return array
	 */
	protected function getTranslationText($arguments) {
		$text = '';
		if (isset($arguments['value']) && $arguments['value'] instanceof TextNode) {
			$text = $arguments['value']->getText();
		} elseif (isset($arguments['value']) && $arguments['value'] instanceof RootNode) {
			foreach ($arguments['value']->getChildNodes() as $childNode) {
				if ($childNode instanceof TextNode) {
					$text .= $childNode->getText();
				} else {
					/** @var $childNodeObjectAccessorNode */
					$text .= '{' . $childNode->getObjectPath() . '}';
				}
			}
		}

		return $text;
	}

	/**
	 * @param $packageKey
	 *
	 * @return Finder
	 */
	protected function findFileSet($packageKey) {
		$package = $this->packageManager->getPackage($packageKey);
		$resourcesPath = $package->getResourcesPath();
		$dirs = array();
		$emberTemplatePath = str_replace('@packageResourcesPath', $resourcesPath, $this->emberTemplateRootPathPattern);
		$dirs[] = $templatePath = str_replace('@packageResourcesPath', $resourcesPath, $this->templateRootPathPattern);
		$dirs[] = $partialPath = str_replace('@packageResourcesPath', $resourcesPath, $this->partialRootPathPattern);
		$dirs[] = $layoutPath = str_replace('@packageResourcesPath', $resourcesPath, $this->layoutRootPathPattern);
		$finder = new Finder();
		if (is_dir($emberTemplatePath)) {
			$dirs[] = $emberTemplatePath;
		}
		$finder->files()
			->in($dirs)
			->name('*.html')
			->name('*.txt')
			->name('*.hbs');
		return $finder;
	}

	/**
	 * @param ParsingState $parsingState
	 */
	protected function findTranslationNodes(ParsingState $parsingState) {
		while ($parsingState->countNodeStack() > 0) {
			$node = $parsingState->popNodeFromStack();
			$this->parseNodes($node->getChildNodes());
		}
	}

	/**
	 * @param array $node
	 * @param array $viewHelperNodes
	 *
	 * @return array
	 */
	protected function parseNodes($nodes) {
		/** @var $node \Neos\FluidAdaptor\Core\Parser\SyntaxTree\AbstractNode */
		foreach ($nodes as $childNode) {
			/** @var $childNode \Neos\FluidAdaptor\Core\Parser\SyntaxTree\ViewHelperNode */
			if (count($childNode->getChildNodes()) > 0) {
				$this->parseNodes($childNode->getChildNodes());
			}
			if ($childNode instanceof \Neos\FluidAdaptor\Core\Parser\SyntaxTree\ViewHelperNode) {
				if (count($childNode->getArguments()) > 0) {
					$this->parseNodes($childNode->getArguments());
				}
				if ($childNode->getViewHelperClassName() == 'Neos\FluidAdaptor\ViewHelpers\TranslateViewHelper') {
					$this->translationNodes[] = $childNode;
				}
			}
		}
	}
}
