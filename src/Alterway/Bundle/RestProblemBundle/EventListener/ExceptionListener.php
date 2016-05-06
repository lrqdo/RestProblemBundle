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
     * @param bool       $original  False when the handling of the exception thrown another exception
     */
    protected function logException(\Exception $exception, $message, $original = true)
    {
        $isCritical = !$exception instanceof HttpExceptionInterface || $exception->getStatusCode() >= 500;
        $context = array('exception' => $exception);
        if (null !== $this->logger) {
            if ($isCritical) {
                $this->logger->critical($message, $context);
            } else {
                $this->logger->error($message, $context);
            }
        } elseif (!$original || $isCritical) {
            error_log($message);
        }
    }
}
