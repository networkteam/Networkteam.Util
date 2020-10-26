<?php
namespace Networkteam\Util\ViewHelpers;

/***************************************************************
 *  (c) 2014 networkteam GmbH - all rights reserved
 ***************************************************************/

/**
 * Switch ViewHelper
 * Fluid implementation of PHP's switch($var) construct
 *
 * @author Claus Due, Wildside A/S
 * @package NwtViewhelpers
 * @subpackage ViewHelpers
 */
class SwitchViewHelper extends \Neos\FluidAdaptor\Core\ViewHelper\AbstractViewHelper implements \Neos\FluidAdaptor\Core\ViewHelper\Facets\ChildNodeAccessInterface
{

    /**
     * NOTE: This property has been introduced via code migration to ensure backwards-compatibility.
     *
     * @see AbstractViewHelper::isOutputEscapingEnabled()
     * @var boolean
     */
    protected $escapeOutput = false;

    /**
     * @var array
     */
    protected $childNodes = array();

    private $backup;

    /**
     * Initialize
     */
    public function initializeArguments()
    {
        $this->registerArgument('value', 'string', 'Variable on which to switch - string, integer or number', true);
        $this->registerArgument('as', 'string',
            'If specified, inserts the matched case tag content as variable using name from "as"');
    }

    /**
     * @param array $childNodes
     */
    public function setChildNodes(array $childNodes)
    {
        $this->childNodes = $childNodes;
    }

    /**
     * Renders the case in the switch which matches variable, else default case
     *
     * @return string
     */
    public function render()
    {
        $content = "";
        $context = $this->renderingContext;
        if ($context->getViewHelperVariableContainer()->exists('Tx_NwtViewhelpers_ViewHelpers_SwitchViewHelper',
            'switchCaseValue')) {
            $this->storeBackup($context);
        }
        $context->getViewHelperVariableContainer()->addOrUpdate('Tx_NwtViewhelpers_ViewHelpers_SwitchViewHelper',
            'switchCaseValue', (string)$this->arguments['value']);
        $context->getViewHelperVariableContainer()->addOrUpdate('Tx_NwtViewhelpers_ViewHelpers_SwitchViewHelper',
            'switchBreakRequested', false);
        $context->getViewHelperVariableContainer()->addOrUpdate('Tx_NwtViewhelpers_ViewHelpers_SwitchViewHelper',
            'switchContinueUntilBreak', false);
        foreach ($this->childNodes as $childNode) {
            if ($childNode instanceof \Neos\FluidAdaptor\Core\Parser\SyntaxTree\ViewHelperNode
                && $childNode->getViewHelperClassName() === 'Networkteam\Util\ViewHelpers\CaseViewHelper'
            ) {
                $content .= $childNode->evaluate($context);
                $shouldBreak = $this->determineBooleanOf($context, 'switchBreakRequested');
                if ($shouldBreak === true) {
                    return $content;
                }
            }
        }
        $context->getViewHelperVariableContainer()->remove('Tx_NwtViewhelpers_ViewHelpers_SwitchViewHelper',
            'switchCaseValue');
        $context->getViewHelperVariableContainer()->remove('Tx_NwtViewhelpers_ViewHelpers_SwitchViewHelper',
            'switchBreakRequested');
        $context->getViewHelperVariableContainer()->remove('Tx_NwtViewhelpers_ViewHelpers_SwitchViewHelper',
            'switchContinueUntilBreak');
        if ($this->backup) {
            $this->restoreBackup($context);
        }
        if ($this->arguments['as']) {
            $this->templateVariableContainer->add($this->arguments['as'], $content);
        } else {
            return $content;
        }
    }

    protected function storeBackup(\Neos\FluidAdaptor\Core\Rendering\RenderingContext $context)
    {
        $this->backup = array(
            $context->getViewHelperVariableContainer()->get('Tx_NwtViewhelpers_ViewHelpers_SwitchViewHelper',
                'switchCaseValue'),
            $this->determineBooleanOf($context, 'switchBreakRequested'),
            $this->determineBooleanOf($context, 'switchContinueUntilBreak')
        );
    }

    protected function determineBooleanOf(\Neos\FluidAdaptor\Core\Rendering\RenderingContext $context, $var)
    {
        if ($context->getViewHelperVariableContainer()->exists('Tx_NwtViewhelpers_ViewHelpers_SwitchViewHelper',
            'switchBreakRequested')) {
            return $context->getViewHelperVariableContainer()->get('Tx_NwtViewhelpers_ViewHelpers_SwitchViewHelper',
                'switchBreakRequested');
        }
    }

    protected function restoreBackup(\Neos\FluidAdaptor\Core\Rendering\RenderingContext $context)
    {
        $context->getViewHelperVariableContainer()->add('Tx_NwtViewhelpers_ViewHelpers_SwitchViewHelper',
            'switchCaseValue', $this->backup[0]);
        $context->getViewHelperVariableContainer()->add('Tx_NwtViewhelpers_ViewHelpers_SwitchViewHelper',
            'switchBreakRequested', $this->backup[1]);
        $context->getViewHelperVariableContainer()->add('Tx_NwtViewhelpers_ViewHelpers_SwitchViewHelper',
            'switchContinueUntilBreak', $this->backup[2]);
    }
}
