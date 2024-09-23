<?php

namespace Alterway\Bundle\RestProblemBundle\EventListener;

use Alterway\Bundle\RestProblemBundle\Problem\Exception;
use Alterway\Bundle\RestProblemBundle\Response\ProblemResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Psr\Log\LoggerInterface;

class ExceptionListener
{
    private bool $debugMode;
    private LoggerInterface $logger;

    public function __construct($debugMode)
    {
        $this->debugMode = $debugMode;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

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

    protected function logException(\Throwable $exception, string $message)
    {
        $isCritical = !$exception instanceof HttpExceptionInterface || $exception->getStatusCode() >= Response::HTTP_INTERNAL_SERVER_ERROR;
        $context = ['exception' => $exception];
        if (null !== $this->logger) {
            if ($isCritical) {
                $this->logger->critical($message, $context);
            } else {
                $this->logger->error($message, $context);
            }
        } elseif ($isCritical) {
            error_log($message);
        }
    }
}
