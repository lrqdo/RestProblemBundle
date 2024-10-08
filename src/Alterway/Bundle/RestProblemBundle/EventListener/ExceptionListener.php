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
        $exception = method_exists($event, 'getThrowable') ? $event->getThrowable() : $event->getException();

        if (extension_loaded('newrelic')) {
            if (
                !$exception instanceof LogicException &&
                !$exception instanceof HttpExceptionInterface &&
                !$exception instanceof Assert\InvalidArgumentException &&
                !$exception instanceof Doctrine\DBAL\Exception\UniqueConstraintViolationException
            ) {
                newrelic_notice_error($exception->getMessage(), $exception);
                newrelic_add_custom_parameter('file', $exception->getFile());
                newrelic_add_custom_parameter('line', $exception->getLine());
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
            if ($exception instanceof HttpExceptionInterface) {
                $this->logger->info($message, $context);
            } else if ($exception instanceof \LogicException) {
                $this->logger->warning($message, $context);
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
