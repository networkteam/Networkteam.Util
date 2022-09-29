<?php
namespace Networkteam\Util\ViewHelpers\Ember;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

use Neos\FluidAdaptor\Core\ViewHelper\AbstractTagBasedViewHelper;

class IncludeTemplatesViewHelper extends AbstractTagBasedViewHelper
{

    /**
     * @var \Neos\FluidAdaptor\View\TemplateView
     */
    protected $templateView;

    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('package', 'string', 'An optional package key to resolve the templates');
    }

    public function render(): string
    {
        if ($this->hasArgument('package')) {
            $packageKey = $this->arguments['package'];
        } else {
            $packageKey = $this->controllerContext->getRequest()->getControllerPackageKey();
        }

        $this->templateView = new \Neos\FluidAdaptor\View\TemplateView();
        $this->templateView->setControllerContext($this->controllerContext);
        $this->templateView->setRenderingContext($this->renderingContext);

        $templates = array();
        $templateRootPath = 'resource://' . $packageKey . '/Private/Ember/Templates/';
        $files = \Neos\Utility\Files::readDirectoryRecursively($templateRootPath, '.hbs');

        foreach ($files as $file) {
            $this->templateView->setTemplatePathAndFilename($file);

            $template = $this->templateView->render();
            $templateName = substr($file, strlen($templateRootPath), -strlen('.hbs'));
            $templates[] = '<script type="text/x-handlebars" data-template-name="' . htmlspecialchars($templateName) . '">' . chr(10) . $template . chr(10) . '</script>';
        }

        return implode("\n", $templates);
    }
}
