<?php

namespace Alterway\Bundle\RestProblemBundle\Problem;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

/*
 * (c) 2013 La Ruche Qui Dit Oui!
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class InvalidQueryForm extends Problem
{
    public function __construct(FormInterface $form)
    {
        $this->title = 'Invalid query form';
        $this->detail = [
            'errors' => $this->buildErrorsTree($form),
        ];
        $this->httpStatus = Response::HTTP_BAD_REQUEST;
    }

    private function buildErrorsTree(FormInterface $form): array
    {
        $errors = [];

        foreach ($form->getErrors() as $key => $error) {
            $errors[$key] = $error->getMessage();
        }

        foreach ($form->all() as $key => $child) {
            if ($child instanceof FormInterface && $err = $this->buildErrorsTree($child)) {
                $errors[$key] = $err;
            }
        }

        return $errors;
    }
}
