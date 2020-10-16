<?php
namespace Networkteam\Util\ViewHelpers;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

use Neos\FluidAdaptor\Core\ViewHelper\AbstractConditionViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

class UnlessViewHelper extends AbstractConditionViewHelper
{

    protected static function evaluateCondition($arguments = null, RenderingContextInterface $renderingContext)
    {
        return !(boolean)$arguments['condition'];
    }
}
