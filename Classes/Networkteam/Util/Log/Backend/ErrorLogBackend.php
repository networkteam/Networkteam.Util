<?php
namespace Networkteam\Util\Log\Backend;

use Neos\Flow\Cli\CommandRequestHandler;
use Neos\Flow\Core\Bootstrap;
use Neos\Flow\Log\Backend\AbstractBackend;
use Neos\Flow\ObjectManagement\ObjectManagerInterface;

/**
 * An error log backend with the same capabilities as the console backend
 *
 */
class ErrorLogBackend extends AbstractBackend
{

    /**
     * @var string
     */
    protected $prefix = '';

    /**
     * @var int
     */
    protected $messageType = 0;

    /**
     * @var bool
     */
    protected $ansi;

    private $disableForCommands = true;

    public function open()
    {
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
        $message,
        $severity = LOG_INFO,
        $additionalData = null,
        $packageKey = null,
        $className = null,
        $methodName = null
    ) {
        if ($severity > $this->severityThreshold) {
            return;
        }

        // TODO error_log will log to output in commands used to compile changes in Development context
        if ($this->disableForCommands && (Bootstrap::$staticObjectManager instanceof ObjectManagerInterface)) {
            $bootstrap = Bootstrap::$staticObjectManager->get(Bootstrap::class);
            /* @var Bootstrap $bootstrap */
            $requestHandler = $bootstrap->getActiveRequestHandler();

            if ($requestHandler instanceof CommandRequestHandler) {
                return;
            }
        }

        if ($this->ansi) {
            $output = LogFormatter::formatAnsi(
                $this->prefix,
                $message,
                $severity,
                $additionalData,
                $packageKey,
                $className,
                $methodName
            );
        } else {
            $output = LogFormatter::format(
                $this->prefix,
                $message,
                $severity,
                $additionalData,
                $packageKey,
                $className,
                $methodName
            );
        }

        error_log($output, $this->messageType);
    }

    public function close()
    {
    }

    /**
     * Set the message type for error_log(...)
     *
     * @param int $messageType
     * @return void
     */
    public function setMessageType(int $messageType): void
    {
        $this->messageType = $messageType;
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

    /**
     * @param bool $ansi
     * @return void
     */
    public function setAnsi(bool $ansi): void
    {
        $this->ansi = $ansi;
    }
}
