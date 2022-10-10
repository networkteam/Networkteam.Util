<?php

namespace Networkteam\Util\ViewHelpers;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

use Networkteam\Util\Domain\DataTransferObject\ListViewConfiguration;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\ActionRequest;
use Neos\Utility\Arrays;
use Neos\FluidAdaptor\Core\ViewHelper\AbstractTagBasedViewHelper;

class SortLinkViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * @var string
     */
    protected $tagName = 'a';

    /**
     * Initialize arguments
     *
     * @return void
     * @api
     */
    public function initializeArguments()
    {
        $this->registerUniversalTagAttributes();
        $this->registerTagAttribute('name', 'string', 'Specifies the name of an anchor');
        $this->registerTagAttribute('rel', 'string', 'Specifies the relationship between the current document and the linked document');
        $this->registerTagAttribute('rev', 'string', 'Specifies the relationship between the linked document and the current document');
        $this->registerTagAttribute('target', 'string', 'Specifies where to open the linked document');
        $this->registerArgument('sortProperty', 'string', '');
        $this->registerArgument('listViewConfiguration', ListViewConfiguration::class, '');
    }

    public function render(): string
    {
        $sortProperty = $this->arguments['sortProperty'];
        /** @var ListViewConfiguration $listViewConfiguration */
        $listViewConfiguration = $this->arguments['listViewConfiguration'];
        $uriBuilder = $this->controllerContext->getUriBuilder();

        $className = '';
        $iconHtml = '';

        if ($sortProperty && $listViewConfiguration->getSortDirection($sortProperty) !== null) {
            $className = 'sorted sort-' . strtolower($listViewConfiguration->getSortDirection($sortProperty));

            if ($listViewConfiguration->getSortDirection($sortProperty) === ListViewConfiguration::DIRECTION_ASCENDING) {
                $iconHtml = '<i class="icon-chevron-up"></i>';
            } else {
                $iconHtml = '<i class="icon-chevron-down"></i>';
            }
        }

        $this->tag->addAttribute('class', $this->tag->getAttribute('class') . $className);

        $request = $this->controllerContext->getRequest();

        $arguments = $request->getArguments();

        $sortDirection = ListViewConfiguration::DIRECTION_DESCENDING;

        if ($listViewConfiguration->getSortDirection($sortProperty) === ListViewConfiguration::DIRECTION_DESCENDING) {
            $sortDirection = ListViewConfiguration::DIRECTION_ASCENDING;
        }

        $arguments['listViewConfiguration'] = $listViewConfiguration->toArray();
        $arguments['listViewConfiguration']['sortDirections'] = [$sortProperty => $sortDirection];

        $uri = $uriBuilder->reset()->uriFor($request->getControllerActionName(), $arguments);

        $this->tag->addAttribute('href', $uri);

        $this->tag->setContent($this->renderChildren() . $iconHtml);
        $this->tag->forceClosingTag(true);

        return $this->tag->render();
    }
}
