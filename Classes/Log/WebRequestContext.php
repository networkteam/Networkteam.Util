<?php

declare(strict_types=1);

namespace Networkteam\Util\Log;

use Neos\Flow\Core\Bootstrap;
use Neos\Flow\Http\HttpRequestHandlerInterface;
use Neos\Flow\ObjectManagement\ObjectManagerInterface;

class WebRequestContext
{
    public static function getContext(): ?array
    {
        if (!(Bootstrap::$staticObjectManager instanceof ObjectManagerInterface)) {
            return null;
        }

        $bootstrap = Bootstrap::$staticObjectManager->get(Bootstrap::class);
        /** @var Bootstrap $bootstrap */
        $requestHandler = $bootstrap->getActiveRequestHandler();
        if (!$requestHandler instanceof HttpRequestHandlerInterface) {
            return null;
        }

        $request = $requestHandler->getHttpRequest();

        $context = [
            'method' => $request->getMethod(),
            'url' => (string)$request->getUri(),
        ];

        if ($request->hasHeader('User-Agent')) {
            $context['userAgent'] = $request->getHeader('User-Agent')[0];
        }

        $requestId = $_SERVER['X-REQUEST-ID'] ?? $_SERVER['HTTP_X_REQUEST_ID'] ?? null;
        if ($requestId) {
            $context['requestId'] = $requestId;
        }

        return $context;
    }
}