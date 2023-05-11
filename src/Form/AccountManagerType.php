<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountManagerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'common.name.label',
                'attr' => [
                    'placeholder' => 'common.name.placeholder',
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'common.email.label',
                'attr' => [
                    'placeholder' => 'common.email.placeholder',
                ],
            ])
            ->add('phone', TextType::class, [
                'label' => 'common.phone.label',
                'attr' => [
                    'placeholder' => 'common.phone.placeholder',
                ],
            ])
            ->add('delete', ButtonType::class, [
                'label' => 'common.delete_manager.label',
                'attr' => [
                    'class' => 'btn btn-square btn-blue js-delete-manager',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'show_delete_button' => true,
        ]);

        $resolver->addAllowedTypes('show_delete_button', 'bool');
    }
}
