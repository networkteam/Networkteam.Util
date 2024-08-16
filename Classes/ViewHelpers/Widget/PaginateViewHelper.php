<?php
namespace Networkteam\Util\ViewHelpers\Widget;

/***************************************************************
 *  (c) 2014 networkteam GmbH - all rights reserved
 ***************************************************************/

use Neos\Flow\Persistence\QueryResultInterface;
use Neos\Flow\Annotations as Flow;

/**
 * This ViewHelper renders a Pagination of objects.
 * = Examples =
 * <code title="simple configuration">
 * <f:widget.paginate objects="{blogs}" as="paginatedBlogs" configuration="{itemsPerPage: 5}">
 *   // use {paginatedBlogs} as you used {blogs} before, most certainly inside
 *   // a <f:for> loop.
 * </f:widget.paginate>
 * </code>
 * <code title="full configuration">
 * <f:widget.paginate objects="{blogs}" as="paginatedBlogs" configuration="{itemsPerPage: 5, insertAbove: 1, insertBelow: 0, maximumNumberOfLinks: 10}">
 *   // This example will display at the maximum 10 links and tries to the settings
 *   // pagesBefore and pagesAfter into account to get the best result
 * </f:widget.paginate>
 * </code>
 * = Performance characteristics =
 * In the above example, it looks like {blogs} contains all Blog objects, thus
 * you might wonder if all objects were fetched from the database.
 * However, the blogs are NOT fetched from the database until you actually use them,
 * so the paginate ViewHelper will adjust the query sent to the database and receive
 * only the small subset of objects.
 * So, there is no negative performance overhead in using the Paginate Widget.
 *
 * @api
 */
class PaginateViewHelper extends \Neos\FluidAdaptor\Core\Widget\AbstractWidgetViewHelper
{

    /**
     * @Flow\Inject
     * @var \Networkteam\Util\ViewHelpers\Widget\Controller\PaginateController
     */
    protected $controller;

    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('objects', QueryResultInterface::class, 'Objects', true);
        $this->registerArgument('as', 'string', 'as', true);
        $this->registerArgument('configuration', 'array', 'Widget configuration', false,
            ['itemsPerPage' => 10, 'insertAbove' => false, 'insertBelow' => true, 'maximumNumberOfLinks' => 99]);
    }

    public function render(): string
    {
        return $this->initiateSubRequest();
    }
}
