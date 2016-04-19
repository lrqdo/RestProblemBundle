<?php

use Alterway\Bundle\RestProblemBundle\Problem\Exception;

class ExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_blanks_title_and_detail_when_exception_should_not_be_verbose()
    {
        $exception = new Exception(
            new \LogicException('Something bad has happened.'),
            $isVerbose = false
        );

        $this->assertEmpty($exception->getTitle());
        $this->assertEmpty($exception->getDetail());
    }
}
