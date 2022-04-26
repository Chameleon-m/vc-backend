<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class IsValidOwner extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public string $message = 'Cannot set owner to a different user.';
    public string $anonymousMessage = 'Cannot set owner unless you are authenticated.';
}
