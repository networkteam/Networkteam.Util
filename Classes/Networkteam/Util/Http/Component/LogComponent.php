<?php

namespace Networkteam\Util\Http\Component;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Http\Component\ComponentContext;
use Neos\Flow\Http\Component\ComponentInterface;

class LogComponent implements ComponentInterface
{

    /**
     * @Flow\Inject
     * @var \Networkteam\Util\Log\HttpLoggerInterface
     */
    protected $httpLogger;

    public function __construct(array $options = array())
    {

    }

    public function handle(ComponentContext $componentContext)
    {
        $request = $componentContext->getHttpRequest();

        $this->httpLogger->info($request->getMethod() . ' ' . $request->getUri());
    }

}