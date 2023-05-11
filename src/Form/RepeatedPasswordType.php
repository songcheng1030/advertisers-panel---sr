<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RepeatedPasswordType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'type' => PasswordType::class,
            'invalid_message' => 'users.password.invalid_message',
            'options' => [
                'attr' => [
                    'class' => 'password-field',
                    'autocomplete' => 'new-password',
                ],
            ],
            'required' => true,
            'first_options' => [
                'label' => 'users.password.first_options.label',
                'attr' => [
                    'placeholder' => 'users.password.first_options.placeholder',
                    'autocomplete' => 'new-password',
                ],
            ],
            'second_options' => [
                'label' => 'users.password.second_options.label',
                'attr' => [
                    'placeholder' => 'users.password.second_options.placeholder',
                    'autocomplete' => 'new-password',
                ],
            ],
        ]);
    }

    public function getParent()
    {
        return RepeatedType::class;
    }
}
