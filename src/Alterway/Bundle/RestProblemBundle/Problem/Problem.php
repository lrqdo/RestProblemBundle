<?php

namespace Alterway\Bundle\RestProblemBundle\Problem;

/*
 * (c) 2013 La Ruche Qui Dit Oui!
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Problem implements ProblemInterface
{
    protected string $problemType = 'http://not-specified-yet';
    protected string $title;
    protected array $detail;
    protected int $httpStatus;

    public function getProblemType(): string
    {
        return $this->problemType;
    }

    public function setProblemType(string $problemType): void
    {
        $this->problemType = $problemType;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDetail(): array
    {
        return $this->detail;
    }

    public function setDetail(array $detail): void
    {
        $this->detail = $detail;
    }

    public function getHttpStatus(): int
    {
        return $this->httpStatus;
    }

    public function setHttpStatus(int $httpStatus): void
    {
        $this->httpStatus = $httpStatus;
    }
}
