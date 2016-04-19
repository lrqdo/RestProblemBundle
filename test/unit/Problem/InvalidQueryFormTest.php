<?php

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormError;

/**
 * @covers \Alterway\Bundle\RestProblemBundle\Problem\InvalidQueryForm
 */
class InvalidQueryFormTest extends PHPUnit_Framework_TestCase
{
    public function testDetailsOfInvalidFormAreGiven()
    {
        $form = $this->getMock(FormInterface::class);
        $error = $this->getMockBuilder(FormError::class)
            ->disableOriginalConstructor()
            ->getMock();

        $error->method('getMessage')->willReturn('an error occured');

        $form
                ->expects($this->once())
                ->method('getErrors')
                ->will($this->returnValue(array('field1' => $error)))
        ;
        $form
                ->expects($this->once())
                ->method('all')
                ->will($this->returnValue(array()))
        ;

        $object = new \Alterway\Bundle\RestProblemBundle\Problem\InvalidQueryForm($form);
        $expected = array('field1' => 'an error occured');
        $this->assertEquals($expected, $object->getDetail()['errors']);
    }

    public function testNoProblemIsFoundWhenFormIsValid()
    {
        $form = $this->getMock(FormInterface::class);

        $form
                ->expects($this->once())
                ->method('getErrors')
                ->will($this->returnValue(array()))
        ;
        $form
                ->expects($this->once())
                ->method('all')
                ->will($this->returnValue(array()))
        ;

        $object = new \Alterway\Bundle\RestProblemBundle\Problem\InvalidQueryForm($form);
        $expected = array();
        $this->assertEquals($expected, $object->getDetail()['errors']);
    }
}
