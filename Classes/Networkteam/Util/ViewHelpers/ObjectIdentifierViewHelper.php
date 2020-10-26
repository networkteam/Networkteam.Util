<?php
namespace Networkteam\Util\ViewHelpers;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

use Neos\Flow\Annotations as Flow;

class ObjectIdentifierViewHelper extends \Neos\FluidAdaptor\Core\ViewHelper\AbstractViewHelper
{

    /**
     * NOTE: This property has been introduced via code migration to ensure backwards-compatibility.
     *
     * @see AbstractViewHelper::isOutputEscapingEnabled()
     * @var boolean
     */
    protected $escapeOutput = false;

    /**
     * @var \Neos\Flow\Persistence\PersistenceManagerInterface
     * @Flow\Inject
     */
    protected $persistenceManager;

    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('object', 'object', '');
    }

    public function render(): string
    {
        if ($this->hasArgument('object')) {
            $object = $this->arguments['object'];
        } else {
            $object = $this->renderChildren();
            if ($object === null) {
                return '';
            }
        }
        return (string)$this->persistenceManager->getIdentifierByObject($object);
    }
}
