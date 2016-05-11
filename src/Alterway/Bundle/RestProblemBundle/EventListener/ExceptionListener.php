<?php

namespace Alterway\Bundle\RestProblemBundle\EventListener;

use Alterway\Bundle\RestProblemBundle\Problem\Exception;
use Alterway\Bundle\RestProblemBundle\Response\ProblemResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class ExceptionListener
{
    private $debugMode;
    private $exception;

    public function __construct($debugMode)
    {
        $this->debugMode = $debugMode;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $this->exception = $event->getException();
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        if ($event->getResponse()->isServerError()) {
            $event->setResponse(new ProblemResponse(new Exception($this->exception, $this->debugMode)));
        }
    }
}
