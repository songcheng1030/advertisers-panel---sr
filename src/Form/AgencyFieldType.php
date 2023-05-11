<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AgencyFieldType extends AbstractType
{
    const TYPE_AGENCY = 1;
    const TYPE_NETWORK = 2;
    const TYPE_DIRECT_ADVERTISER = 3;

    public static $typesNames = [
        self::TYPE_AGENCY => 'agencies.type.agency',
        self::TYPE_NETWORK => 'agencies.type.network',
        self::TYPE_DIRECT_ADVERTISER => 'agencies.type.direct_advertiser',
    ];

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => array_flip(self::$typesNames),
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
