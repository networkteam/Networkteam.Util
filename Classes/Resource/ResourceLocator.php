<?php
namespace Networkteam\Util\Resource;

/***************************************************************
 *  (c) 2014 networkteam GmbH - all rights reserved
 ***************************************************************/

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ControllerContext;
use Neos\Flow\ResourceManagement\PersistentResource;
use Neos\Flow\ResourceManagement\ResourceManager;
use Neos\FluidAdaptor\Core\ViewHelper\Exception\InvalidVariableException;

class ResourceLocator
{

    /**
     * @Flow\Inject
     * @var ResourceManager
     */
    protected $resourceManager;

    /**
     * @var ControllerContext
     */
    protected $controllerContext;

    /**
     * @Flow\Inject
     * @var \Neos\Flow\I18n\Service
     */
    protected $i18nService;

    public function getResourceUri(
        string $path = null,
        string $package = null,
        PersistentResource $resource = null,
        bool $localize = true,
        bool $appendCacheBuster = true
    ): string
    {
        $cacheBuster = '';

        if ($resource !== null) {
            $uri = $this->resourceManager->getPublicPersistentResourceUri($resource);
            if ($uri === false) {
                $uri = '404-Resource-Not-Found';
            }
        } else {
            if ($path === null) {
                throw new InvalidVariableException('The ResourceViewHelper did neither contain a valuable "resource" nor "path" argument.',
                    1353512742);
            }
            if ($package === null) {
                $package = $this->controllerContext->getRequest()->getControllerPackageKey();
            }
            if (strpos($path, 'resource://') === 0) {
                $matches = array();
                if (preg_match('#^resource://([^/]+)/Public/(.*)#', $path, $matches) === 1) {
                    $package = $matches[1];
                    $path = $matches[2];
                } else {
                    throw new InvalidVariableException(sprintf('The path "%s" which was given to the ResourceViewHelper must point to a public resource.',
                        $path), 1353512639);
                }
            }
            if ($localize === true) {
                $resourcePath = 'resource://' . $package . '/Public/' . $path;
                $localizedResourcePathData = $this->i18nService->getLocalizedFilename($resourcePath);
                $matches = array();
                if (preg_match('#resource://([^/]+)/Public/(.*)#', current($localizedResourcePathData),
                        $matches) === 1) {
                    $package = $matches[1];
                    $path = $matches[2];
                }
            }

            if ($appendCacheBuster === true) {
                $resourcePath = 'resource://' . $package . '/Public/' . $path;
                if (is_file($resourcePath)) {
                    $resourceModificationTimestamp = filemtime($resourcePath);
                    if ($resourceModificationTimestamp !== false) {
                        $cacheBuster = '?' . $resourceModificationTimestamp;
                    }
                }
            }

            $uri = $this->resourceManager->getPublicPackageResourceUri($package, $path) . $cacheBuster;
        }
        return $uri;
    }

    public function setContext(ControllerContext $context): void
    {
        $this->controllerContext = $context;
    }
}
