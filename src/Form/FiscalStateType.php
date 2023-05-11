<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiscalStateType extends AbstractType
{
    const STATE_AUTONOMOUS = 1;
    const STATE_COMPANY = 2;

    public static $fiscalStateNames = [
        self::STATE_AUTONOMOUS => 'fiscal_state.autonomous',
        self::STATE_COMPANY => 'fiscal_state.company',
    ];

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => array_flip(self::$fiscalStateNames),
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
