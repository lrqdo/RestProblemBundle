<?php

use Alterway\Bundle\RestProblemBundle\Problem\Problem;
use Alterway\Bundle\RestProblemBundle\Response\ProblemResponse;

class ProblemResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_does_not_contain_title_nor_detail_when_debug_mode_is_off()
    {
        $problem = $this->createProblem();
        $response = new ProblemResponse($problem, 400, array(), $debugMode = false);

        $this->assertJsonStringEqualsJsonString(
            json_encode(
                array(
                    'problemType' => 'exception',
                )
            ),
            $response->getContent()
        );
    }

    /**
     * @test
     */
    public function it_contains_title_and_detail_when_debug_mode_is_on()
    {
        $problem = $this->createProblem();
        $response = new ProblemResponse($problem, 400, array(), $debugMode = true);

        $this->assertJsonStringEqualsJsonString(
            json_encode(
                array(
                    'problemType' => 'exception',
                    'title' => 'An error occured',
                    'detail' => 'Sensitive details',
                )
            ),
            $response->getContent()
        );
    }

    private function createProblem()
    {
        $problem = new Problem();

        $problem->setProblemType('exception');
        $problem->setTitle('An error occured');
        $problem->setDetail('Sensitive details');

        return $problem;
    }
}
