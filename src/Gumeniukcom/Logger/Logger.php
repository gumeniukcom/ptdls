<?php declare(strict_types=1);

namespace Gumeniukcom\Logger;

use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger as MLogger;
use Psr\Log\LoggerInterface;

class Logger implements LoggerInterface
{

    public const LEVEL_DEBUG = MLogger::DEBUG;

    public const LEVEL_INFO = MLogger::INFO;

    public const LEVEL_NOTICE = MLogger::NOTICE;

    public const LEVEL_WARNING = MLogger::WARNING;

    public const LEVEL_ERROR = MLogger::ERROR;

    public const LEVEL_CRITICAL = MLogger::CRITICAL;

    public const LEVEL_ALERT = MLogger::ALERT;

    public const LEVEL_EMERGENCY = MLogger::EMERGENCY;

    private MLogger $logger;

    /**
     * @var bool
     */
    private bool $clearContextRule = true;

    public function __construct(string $name, int $logLevel = self::LEVEL_DEBUG)
    {
        $this->logger = new MLogger($name);

        $formatter = new JsonFormatter();

        $stream = new StreamHandler(
            'php://stdout',
            $logLevel,
        );
        $stream->setFormatter($formatter);

        $this->logger->pushHandler($stream);
    }

    /**
     * @param bool $clear
     */
    public function setClearContextRule(bool $clear): void
    {
        $this->clearContextRule = $clear;
    }


    /**
     * @param string $message
     * @param array $context
     */
    public function emergency($message, array $context = [])
    {
        $this->logger->emergency($message, $this->clearContext($context));
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function alert($message, array $context = [])
    {
        $this->logger->alert($message, $this->clearContext($context));
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function critical($message, array $context = [])
    {
        $this->logger->critical($message, $this->clearContext($context));
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function error($message, array $context = [])
    {
        $this->logger->error($message, $this->clearContext($context));
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function warning($message, array $context = [])
    {
        $this->logger->warning($message, $this->clearContext($context));
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function notice($message, array $context = [])
    {
        $this->logger->notice($message, $this->clearContext($context));
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function info($message, array $context = [])
    {
        $this->logger->info($message, $this->clearContext($context));
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function debug($message, array $context = [])
    {
        $this->logger->debug($message, $this->clearContext($context));
    }

    /**
     * @param mixed $level
     * @param string $message
     * @param array $context
     */
    public function log($level, $message, array $context = [])
    {
        $this->logger->log($level, $message, $this->clearContext($context));
    }

    /**
     * @param array $context
     * @return array
     */
    private function clearContext(array $context = []): array
    {
        if (!$this->clearContextRule) {
            return $context;
        }

        //TODO: Clear context

        return $context;
    }
}