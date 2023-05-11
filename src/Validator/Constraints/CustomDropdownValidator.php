<?php

namespace App\Validator\Constraints;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Contracts\Translation\TranslatorInterface;

class CustomDropdownValidator extends ConstraintValidator
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * CustomDropdownValidator constructor.
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof CustomDropdown) {
            throw new UnexpectedTypeException($constraint, CustomDropdown::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!$value instanceof PersistentCollection && !$value instanceof ArrayCollection) {
            throw new UnexpectedValueException($value, 'PersistentCollection|ArrayCollection');
        }

        if (0 == count($value)) {
            $this->context->buildViolation($this->translator->trans($constraint->requiredMessage))
                ->addViolation();

            return;
        }
    }
}
