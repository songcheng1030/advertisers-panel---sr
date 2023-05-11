<?php

namespace App\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AgenciesType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'name' => 'agencies',
        ]);

        $resolver->addAllowedTypes('name', 'string');
    }

    public function getParent()
    {
        return EntityType::class;
    }
}
