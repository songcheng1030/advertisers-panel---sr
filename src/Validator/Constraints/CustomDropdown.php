<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class CustomDropdown extends Constraint
{
    public $requiredMessage = 'common.select_at_least_one';

    /**
     * CustomDropdown constructor.
     *
     * @param null $options
     */
    public function __construct($options = null)
    {
        parent::__construct($options);
    }

    public function validatedBy()
    {
        return \get_class($this) . 'Validator';
    }
}
