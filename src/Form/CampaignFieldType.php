<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CampaignFieldType extends AbstractType
{
    const TYPE_DEAL = 1;
    const TYPE_CAMPAIGN = 2;

    public static $typesNames = [
        self::TYPE_DEAL => 'agencies.type.deal',
        self::TYPE_CAMPAIGN => 'agencies.type.campaign',
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
