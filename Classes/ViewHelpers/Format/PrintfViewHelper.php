<?php
namespace Networkteam\Util\ViewHelpers\Format;

/***************************************************************
 *  (c) 2014 networkteam GmbH - all rights reserved
 ***************************************************************/
class PrintfViewHelper extends \Neos\FluidAdaptor\Core\ViewHelper\AbstractViewHelper
{

    /**
     * NOTE: This property has been introduced via code migration to ensure backwards-compatibility.
     *
     * @see AbstractViewHelper::isOutputEscapingEnabled()
     * @var boolean
     */
    protected $escapeOutput = false;

    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('arguments', 'array', 'sprintf arguments');
        $this->registerArgument('format', 'string', 'sprintf format');
    }

    public function render(): string
    {
        if ($this->hasArgument('format')) {
            $format = $this->arguments['format'];
        } else {
            $format = $this->renderChildren();
        }

        return vsprintf($format, $this->arguments['arguments']);
    }
}
