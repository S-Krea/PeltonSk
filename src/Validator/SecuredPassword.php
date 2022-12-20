<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class SecuredPassword extends Constraint
{
    public string $message = 'secured_password.message';
}
