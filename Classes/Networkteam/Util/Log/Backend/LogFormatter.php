<?php
namespace Networkteam\Util\Log\Backend;

use Neos\Flow\Log\PlainTextFormatter;

class LogFormatter
{

    const FG_BLACK = "\033[0;30m";
    const FG_WHITE = "\033[1;37m";
    const FG_GRAY = "\033[0;37m";
    const FG_BLUE = "\033[0;34m";
    const FG_CYAN = "\033[0;36m";
    const FG_YELLOW = "\033[1;33m";
    const FG_RED = "\033[0;31m";
    const FG_GREEN = "\033[0;32m";

    const BG_CYAN = "\033[46m";
    const BG_GREEN = "\033[42m";
    const BG_RED = "\033[41m";
    const BG_YELLOW = "\033[43m";
    const BG_WHITE = "\033[47m";

    const END = "\033[0m";

    const TAG_FORMATS = [
        'emergency' => self::FG_BLACK . self::BG_RED . '|' . self::END,
        'alert' => self::FG_BLACK . self::BG_YELLOW . '|' . self::END,
        'critical' => self::FG_BLACK . self::BG_CYAN . '|' . self::END,
        'error' => self::FG_RED . '|' . self::END,
        'warning' => self::FG_YELLOW . '|' . self::END,
        'notice' => self::FG_WHITE . '|' . self::END,
        'info' => self::FG_GREEN . '|' . self::END,
        'debug' => self::FG_BLUE . '|' . self::END,
        'tag' => self::FG_GRAY . '|' . self::END,
    ];

    public static function format(
        ?string $prefix,
        string $message,
        int $severity,
        $additionalData,
        ?string $packageKey,
        ?string $className,
        ?string $methodName
    ): string {
        $severityLabel = 'UNKNOWN  ';
        switch ($severity) {
            case LOG_EMERG:
                $severityLabel = 'EMERGENCY';
                break;
            case LOG_ALERT:
                $severityLabel = 'ALERT    ';
                break;
            case LOG_CRIT:
                $severityLabel = 'CRITICAL ';
                break;
            case LOG_ERR:
                $severityLabel = 'ERROR    ';
                break;
            case LOG_WARNING:
                $severityLabel = 'WARNING  ';
                break;
            case LOG_NOTICE:
                $severityLabel = 'NOTICE   ';
                break;
            case LOG_INFO:
                $severityLabel = 'INFO     ';
                break;
            case LOG_DEBUG:
                $severityLabel = 'DEBUG    ';
                break;
        }

        $output = $severityLabel . ' ' . $message;
        if (!empty($prefix)) {
            $output = $prefix . ' ' . $output;
        }
        if (!empty($packageKey)) {
            $output .= sprintf(' package=%s', $packageKey);
        }
        if (!empty($className)) {
            $output .= sprintf(' class=%s', $className);
        }
        if (!empty($methodName)) {
            $output .= sprintf(' method=%s', $methodName);
        }
        if (!empty($additionalData)) {
            $output .= PHP_EOL . (new PlainTextFormatter($additionalData))->format();
        }
        return self::formatOutput($output);
    }

    public static function formatAnsi(
        ?string $prefix,
        string $message,
        int $severity,
        $additionalData,
        ?string $packageKey,
        ?string $className,
        ?string $methodName
    ): string {
        $severityLabel = '<notice>UNKNOWN</notice>  ';
        switch ($severity) {
            case LOG_EMERG:
                $severityLabel = '<emergency>EMERGENCY</emergency>';
                break;
            case LOG_ALERT:
                $severityLabel = '<alert>ALERT</alert>    ';
                break;
            case LOG_CRIT:
                $severityLabel = '<critical>CRITICAL</critical> ';
                break;
            case LOG_ERR:
                $severityLabel = '<error>ERROR</error>    ';
                break;
            case LOG_WARNING:
                $severityLabel = '<warning>WARNING</warning>  ';
                break;
            case LOG_NOTICE:
                $severityLabel = '<notice>NOTICE</notice>   ';
                break;
            case LOG_INFO:
                $severityLabel = '<info>INFO</info>     ';
                break;
            case LOG_DEBUG:
                $severityLabel = '<debug>DEBUG</debug>    ';
                break;
        }

        $output = $severityLabel . ' ' . $message;
        if (!empty($prefix)) {
            $output = sprintf('<tag>%s</tag> %s', $prefix, $output);
        }
        if (!empty($packageKey)) {
            $output .= sprintf(' <tag>package=</tag>%s', $packageKey);
        }
        if (!empty($className)) {
            $output .= sprintf(' <tag>class=</tag>%s', $className);
        }
        if (!empty($methodName)) {
            $output .= sprintf(' <tag>method=</tag>%s', $methodName);
        }
        if (!empty($additionalData)) {
            $output .= PHP_EOL . (new PlainTextFormatter($additionalData))->format();
        }
        return self::formatOutput($output);
    }

    private static function formatOutput(string $output): string
    {
        do {
            $lastOutput = $output;
            $output = preg_replace_callback('|(<([^>]+?)>(.*?)</\2>)|s',
                function ($matches) {
                    $format = isset(self::TAG_FORMATS[$matches[2]]) ? self::TAG_FORMATS[$matches[2]] : '|';
                    return str_replace('|', $matches[3], $format);
                }, $output);
        } while ($lastOutput !== $output);
        return $output;
    }
}