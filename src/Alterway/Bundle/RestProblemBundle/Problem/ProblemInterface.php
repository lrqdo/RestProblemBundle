<?php

namespace Alterway\Bundle\RestProblemBundle\Problem;

/*
 * (c) 2013 La Ruche Qui Dit Oui!
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Problem
 * 
 * @see http://tools.ietf.org/html/draft-nottingham-http-problem-03
 */
interface ProblemInterface
{
    public function getProblemType(): string;

    public function setProblemType(string $problemType);

    public function getTitle(): string;

    public function setTitle(string $title);

    public function getDetail(): array;

    public function setDetail(array $detail);

    public function getHttpStatus(): int;

    public function setHttpStatus(int $httpStatus);
}
