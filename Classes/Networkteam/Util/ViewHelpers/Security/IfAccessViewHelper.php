<?php
namespace Networkteam\Util\ViewHelpers\Security;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

use \TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

class IfAccessViewHelper extends \Neos\FluidAdaptor\ViewHelpers\Security\IfAccessViewHelper
{

    CONST PRIVILEGE_TARGET_SEPARATOR = '|';

    CONST CONDITION_MODE_AND = 'and';

    CONST CONDITION_MODE_OR= 'or';

    public function initializeArguments()
    {
        parent::initializeArguments();
        $description = sprintf('One or more privilege target identifier (separated by "%s")',self::PRIVILEGE_TARGET_SEPARATOR);
        $this->overrideArgument('privilegeTarget', 'string', $description, true);
        $this->registerArgument('mode', 'string', 'Condition mode for multiple privelege targets', false, self::CONDITION_MODE_OR);
    }

    /**
     * The condition evaluates to true if one of the privilege targets is granted
     *
     * @param null $arguments
     * @param RenderingContextInterface $renderingContext
     * @return boolean
     */
    protected static function evaluateCondition($arguments, RenderingContextInterface $renderingContext)
    {
        $privilegeManager = static::getPrivilegeManager($renderingContext);
        $privilegeTargets = explode(self::PRIVILEGE_TARGET_SEPARATOR, $arguments['privilegeTarget']);

        $privilegeTargetGrantedCount = 0;
        foreach ($privilegeTargets as $privilegeTarget) {
            $isPrivilegeTargetGranted = $privilegeManager->isPrivilegeTargetGranted($privilegeTarget, $arguments['parameters']);

            if ($isPrivilegeTargetGranted) {
                $privilegeTargetGrantedCount++;
            }
        }

        if (isset($arguments['mode']) && $arguments['mode'] === self::CONDITION_MODE_AND)
        {
            return $privilegeTargetGrantedCount === count($privilegeTargets);
        } else {
            return $privilegeTargetGrantedCount > 0;
        }
    }
}
