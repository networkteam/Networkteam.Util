<?php
namespace Networkteam\Util\Log\Backend;


use Neos\Flow\Log\Backend\AbstractBackend;
use Neos\Flow\Log\Exception\CouldNotOpenResourceException;
use Neos\Flow\Log\PlainTextFormatter;

/**
 * An extended console log backend with additional prefix and output of package key, class and method
 */
class ConsoleBackend extends AbstractBackend
{
    /**
     * An array of severity labels, indexed by their integer constant
     * @var array
     */
    protected $severityLabels;

    /**
     * Stream name to use (stdout, stderr)
     * @var string
     */
    protected $streamName = 'stdout';

    /**
     * @var resource
     */
    protected $streamHandle;

    /**
     * @var string
     */
    protected $prefix = '';

    /**
     * Carries out all actions necessary to prepare the logging backend, such as opening
     * the log file or opening a database connection.
     *
     * @return void
     * @throws CouldNotOpenResourceException
     * @api
     */
    public function open(): void
    {
        $this->severityLabels = [
            LOG_EMERG => 'emergency',
            LOG_ALERT => 'alert',
            LOG_CRIT => 'critical',
            LOG_ERR => 'error',
            LOG_WARNING => 'warning',
            LOG_NOTICE => 'notice',
            LOG_INFO => 'info',
            LOG_DEBUG => 'debug',
        ];

        $this->streamHandle = fopen('php://' . $this->streamName, 'w');
        if (!is_resource($this->streamHandle)) {
            throw new CouldNotOpenResourceException('Could not open stream "' . $this->streamName . '" for write access.',
                1310986609);
        }
    }

    /**
     * Appends the given message along with the additional information into the log.
     *
     * @param string $message The message to log
     * @param integer $severity One of the LOG_* constants
     * @param mixed $additionalData A variable containing more information about the event to be logged
     * @param string $packageKey Key of the package triggering the log (determined automatically if not specified)
     * @param string $className Name of the class triggering the log (determined automatically if not specified)
     * @param string $methodName Name of the method triggering the log (determined automatically if not specified)
     * @return void
     * @api
     */
    public function append(
        string $message,
        int $severity = LOG_INFO,
        $additionalData = null,
        string $packageKey = null,
        string $className = null,
        string $methodName = null
    ): void {
        if ($severity > $this->severityThreshold) {
            return;
        }

        $data = [
            'eventTime' => (new \DateTime('now'))->format(DATE_RFC3339),
            'severity' => $this->severityLabels[$severity] ?? 'unknown',
            'message' => implode(' ', [trim($this->prefix), $message]),
            'class' => $className,
            'method' => $methodName,
            'package' => $packageKey, // Drop since 'class' is FQDN?
            'additionalData' => $additionalData,
            'source' => 'neos-flow'
        ];
        if (is_resource($this->streamHandle)) {
            fputs($this->streamHandle, json_encode($data, JSON_THROW_ON_ERROR) . PHP_EOL);
        }
    }

    /**
     * Carries out all actions necessary to cleanly close the logging backend, such as
     * closing the log file or disconnecting from a database.
     *
     * Note: for this backend we do nothing here and rely on PHP to close the stream handle
     * when the request ends. This is to allow full logging until request end.
     *
     * @return void
     * @api
     * @todo revise upon resolution of http://forge.typo3.org/issues/9861
     */
    public function close(): void
    {
    }

    /**
     * Set the stream name (stdout, stderr) to use
     *
     * @param string $streamName
     * @return void
     */
    public function setStreamName(string $streamName): void
    {
        $this->streamName = $streamName;
    }

    /**
     * Set the prefix to prepend to every logged message
     *
     * @param string $prefix
     * @return void
     */
    public function setPrefix(string $prefix): void
    {
        $this->prefix = $prefix;
    }

}
