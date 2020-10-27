<?php
namespace Networkteam\Util\ViewHelpers\Bootstrap;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

use Neos\FluidAdaptor\Core\ViewHelper\AbstractViewHelper;

class SeverityClassViewHelper extends AbstractViewHelper
{

    const SEVERITY_NOTICE = 'Notice';
    const SEVERITY_WARNING = 'Warning';
    const SEVERITY_ERROR = 'Error';
    const SEVERITY_OK = 'OK';

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
        $this->registerArgument('severity', 'string', 'One of self::SEVERITY_*');
    }

    /**
     * Returns the bootstrap class for the given severity
     *
     * @param string $severity
     * @return string
     */
    public function render(): string
    {
        switch ($this->arguments['severity']) {
            case self::SEVERITY_OK:
                return 'alert alert-success';
                break;
            case self::SEVERITY_WARNING:
                return 'alert';
                break;
            case self::SEVERITY_NOTICE:
                return 'alert alert-info';
                break;
            case self::SEVERITY_ERROR:
                return 'alert alert-error';
                break;
        }
    }
}
