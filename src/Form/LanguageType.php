<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LanguageType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => [
                'Español' => 'es',
                'Inglés' => 'en',
            ],
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
