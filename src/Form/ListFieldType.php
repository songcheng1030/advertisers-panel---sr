<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ListFieldType extends AbstractType
{
    const TYPE_WHITE_LIST = 1;
    const TYPE_BLACK_LIST = 2;

    public static $typeNames = [
        self::TYPE_WHITE_LIST => 'common.white_list',
        self::TYPE_BLACK_LIST => 'common.black_list',
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
