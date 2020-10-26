<?php
namespace Networkteam\Util\ViewHelpers\Uri;

/***************************************************************
 *  (c) 2014 networkteam GmbH - all rights reserved
 ***************************************************************/

use Neos\Flow\Annotations as Flow;
use \Neos\Flow\ResourceManagement\PersistentResource as PersistentResource;

class ResourceViewHelper extends \Neos\FluidAdaptor\Core\ViewHelper\AbstractViewHelper
{

    /**
     * NOTE: This property has been introduced via code migration to ensure backwards-compatibility.
     *
     * @see AbstractViewHelper::isOutputEscapingEnabled()
     * @var boolean
     */
    protected $escapeOutput = false;

    /**
     * @var \Networkteam\Util\Resource\ResourceLocator
     * @Flow\Inject
     */
    protected $resourceLocator;

    /**
     * Initialize the arguments.
     *
     * @return void
     * @throws \Neos\FluidAdaptor\Core\ViewHelper\Exception
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('path', 'string', 'path to render', true);
        $this->registerArgument('package', 'string', 'package', false);
        $this->registerArgument('resource', PersistentResource::class, 'property to search', false);
        $this->registerArgument('localize', 'boolean', 'attemped resource location', false);
        $this->registerArgument('cacheBuster', 'boolean', 'cache buster', false);
    }

    /**
     * Render the URI to the resource. The filename is used from child content.
     *
     * @return string The absolute URI to the resource
     * @throws \Neos\FluidAdaptor\Core\ViewHelper\Exception\InvalidVariableException
     * @api
     */
    public function render(): string
    {
        $this->resourceLocator->setContext($this->controllerContext);
        $path = $this->arguments['path'];
        $package = $this->arguments['package'];
        $resource = $this->arguments['resource'];
        $localize = $this->arguments['localize'];
        $cacheBuster = $this->arguments['cacheBuster'];

        return $this->resourceLocator->getResourceUri($path, $package, $resource, $localize, $cacheBuster);
    }
}
