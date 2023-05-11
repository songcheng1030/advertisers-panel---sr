<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CostFieldType extends AbstractType
{
    const TYPE_CPM = 1;
    const TYPE_CPV = 2;

    public static $typeNames = [
        self::TYPE_CPM => 'common.cpm.label',
        self::TYPE_CPV => 'common.cpv.label',
    ];

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => array_flip(self::$typeNames),
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
