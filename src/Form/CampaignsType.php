<?php

namespace App\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CampaignsType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'name' => 'campaigns',
        ]);

        $resolver->addAllowedTypes('name', 'string');
    }

    public function getParent()
    {
        return EntityType::class;
    }
}
