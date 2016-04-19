<?php

namespace Alterway\Bundle\RestProblemBundle\Problem;

use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class Exception extends Problem
{
    public function __construct(\Exception $exception, $isVerbose = false)
    {
        $this->problemType = '/exception';
        $this->title = $isVerbose ? $exception->getMessage() : '';
        $this->detail = $isVerbose ? $exception->getTraceAsString() : '';

        switch (true) {
            case $exception instanceof HttpExceptionInterface;
                $this->httpStatus = $exception->getStatusCode();
                break;
            case $exception instanceof \LogicException:
                $this->httpStatus = 400;
                break;
            case $exception instanceof \RuntimeException:
                $this->httpStatus = 500;
                break;
            default:
                $this->httpStatus = 501;
        }
    }
}
