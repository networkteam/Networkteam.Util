<?php

declare(strict_types=1);

namespace Networkteam\Util\Log\ThrowableStorage;

use Neos\Flow\Log\Exception\CouldNotOpenResourceException;
use Neos\Flow\Log\ThrowableStorageInterface;
use Networkteam\Util\Log\WebRequestContext;

final class ConsoleStorage implements ThrowableStorageInterface
{

    /**
     * @var false|resource
     */
    protected $streamHandle;

    /**
     * @throws CouldNotOpenResourceException
     */
    public function __construct(string $streamName)
    {
        $this->streamHandle = fopen($streamName, 'w');

        if (!is_resource($this->streamHandle)) {
            throw new CouldNotOpenResourceException(sprintf('Could not open stream "%s" for write access', $streamName),
                20230726162106);
        }
    }

    public static function createWithOptions(array $options): ThrowableStorageInterface
    {
        if (!array_key_exists('streamName', $options)) {
            throw new \Exception('A stream name must be set');
        }

        $streamName = $options['streamName'];

        if (strpos($streamName, 'php://') === 0) {
            $streamName = substr($streamName, 6);
        }
        return new static('php://' . $streamName);
    }

    public function logThrowable(\Throwable $throwable, array $additionalData = [])
    {
        $data = array_filter([
            'eventTime' => (new \DateTime('now'))->format(DATE_RFC3339),
            'severity' => 'critical',
            'logger' => 'throwableStorage',
            'message' => $throwable->getMessage(),
            'errorLocation' => [
                'filePath' => str_replace(FLOW_PATH_ROOT, '', $throwable->getFile()),
                'lineNumber' => $throwable->getLine(),
                'functionName' => self::getFunctionNameForTrace($throwable->getTrace()),
            ],
            'additionalData' => $additionalData,
            'httpRequest' => WebRequestContext::getContext(),
            'source' => 'neos-flow'
        ]);

        $output = json_encode($data);

        if (is_resource($this->streamHandle)) {
            fwrite($this->streamHandle, $output . PHP_EOL);
        }

        return $output;
    }

    public function setRequestInformationRenderer(\Closure $requestInformationRenderer)
    {
        // No backtrace renderer is needed here
        return $this;
    }

    public function setBacktraceRenderer(\Closure $backtraceRenderer)
    {
        // No backtrace renderer is needed here
        return $this;
    }

    private function getFunctionNameForTrace(?array $trace = null): string
    {
        if ($trace === null) {
            return '<unknown function>';
        }
        if (empty($trace[0]['function'])) {
            return '<none>';
        }
        $functionName = [$trace[0]['function']];
        if (isset($trace[0]['type'])) {
            $functionName[] = $trace[0]['type'];
        }
        if (isset($trace[0]['class'])) {
            $functionName[] = $trace[0]['class'];
        }
        return implode('', array_reverse($functionName));
    }
}
