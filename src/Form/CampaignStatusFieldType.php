<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CampaignStatusFieldType extends AbstractType
{
    const STATUS_ACTIVE = 1;
    const STATUS_PAUSED = 2;
    const STATUS_ARCHIVED = 3;

    public static $statusNames = [
        self::STATUS_ACTIVE => 'common.active',
        self::STATUS_PAUSED => 'common.paused',
        self::STATUS_ARCHIVED => 'common.archived',
    ];

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => array_flip(self::$statusNames),
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
