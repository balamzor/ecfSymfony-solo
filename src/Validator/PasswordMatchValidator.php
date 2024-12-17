<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PasswordMatchValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $form = $this->context->getRoot();

        $plainPassword = $form->get('plainPassword')->getData();
        $confirmPassword = $form->get('confirmPassword')->getData();

        if ($plainPassword !== $confirmPassword) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}