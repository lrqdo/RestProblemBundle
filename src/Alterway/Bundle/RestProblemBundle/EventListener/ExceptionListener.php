<?php

namespace Alterway\Bundle\RestProblemBundle\EventListener;

use Alterway\Bundle\RestProblemBundle\Problem\Exception;
use Alterway\Bundle\RestProblemBundle\Response\ProblemResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Psr\Log\LoggerInterface;

class ExceptionListener
{
    private $debugMode;

    /** @var LoggerInterface */
    private $logger;

    public function __construct($debugMode)
    {
        $this->debugMode = $debugMode;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if (extension_loaded('newrelic')) {
            $nrException = method_exists($event, 'getThrowable') ? $event->getThrowable() : $event->getException();
            if (!$nrException instanceof HttpExceptionInterface) {
                newrelic_notice_error($nrException->getMessage(), $nrException);
                newrelic_add_custom_parameter('file', $nrException->getFile());
                newrelic_add_custom_parameter('line', $nrException->getLine());
            }
        }
        
        $this->logException(
            $exception,
            sprintf(
                'Uncaught PHP Exception %s: "%s" at %s line %s',
                get_class($exception),
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine()
            )
        );

        $event->setResponse(new ProblemResponse(new Exception($exception, $this->debugMode)));
    }

    /**
     * Logs an exception.
     * Taken from https://github.com/symfony/symfony/blob/d97279e942bf3a135634ad90a32f1d5cd05a22ba/src/Symfony/Component/HttpKernel/EventListener/ExceptionListener.php
     *
     * @param \Exception $exception The \Exception instance
     * @param string     $message   The error message to log
     */
    protected function logException(\Exception $exception, $message)
    {
        $isCritical = $exception->getStatusCode() >= 500;
        $context = array('exception' => $exception);
        if (null !== $this->logger) {
            if ($exception instanceof HttpExceptionInterface) {
                $this->logger->info($message, $context);
            } else if ($isCritical) {
                $this->logger->critical($message, $context);
            } else {
                $this->logger->error($message, $context);
            }
        } elseif ($isCritical) {
            error_log($message);
        }
    }
}
