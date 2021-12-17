<?php

namespace Alterway\DemoBundle\Controller;

use Alterway\Bundle\RestProblemBundle\Controller\Annotations\Problem;
use Alterway\Bundle\RestProblemBundle\Response\ProblemResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Alterway\Bundle\RestProblemBundle\Problem\InvalidQueryForm;

class TestController extends AbstractController
{

    public function userWithoutAnnotateAction(Request $request)
    {
        /** @var \Symfony\Component\Form\Form $form */
        $form = $this->get('form.factory')->createNamedBuilder('', FormType::class)
                ->add('email', EmailType::class, array('constraints' => array(new Email())))
                ->add('name', UrlType::class, array('constraints' => array(new Length(15))))
                ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && !$form->isValid()) {
            $problem = new InvalidQueryForm($form);
            return new ProblemResponse($problem);
        }
    }

    /**
     * @Problem
     */
    public function directProblemAction(Request $request)
    {
        /** @var \Symfony\Component\Form\Form $form */
        $form = $this->get('form.factory')->createNamedBuilder('', FormType::class)
                ->add('email', EmailType::class, array('constraints' => array(new Email())))
                ->add('name', UrlType::class, array('constraints' => array(new Length(15))))
                ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && !$form->isValid()) {
            return new InvalidQueryForm($form);
        }
    }

    public function exceptionProblemAction()
    {
        throw new \Exception('Something went wrong!');
    }
}
