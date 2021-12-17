<?php

namespace Alterway\Bundle\RestProblemBundle\Problem;

use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpFoundation\Response;

class Exception extends Problem
{
    public function __construct(\Exception $exception, bool $isVerbose = false)
    {
        $this->problemType = '/exception';
        $this->title = $isVerbose ? $exception->getMessage() : '';
        $this->detail = $isVerbose ? [
            'trace' => $exception->getTraceAsString()
        ] : [];

        switch (true) {
            case $exception instanceof HttpExceptionInterface;
                $this->httpStatus = $exception->getStatusCode();
                break;
            case $exception instanceof \LogicException:
                $this->httpStatus = Response::HTTP_BAD_REQUEST;
                break;
            case $exception instanceof \RuntimeException:
                $this->httpStatus = Response::HTTP_INTERNAL_SERVER_ERROR;
                break;
            default:
                $this->httpStatus = Response::HTTP_NOT_IMPLEMENTED;
        }
    }
}
