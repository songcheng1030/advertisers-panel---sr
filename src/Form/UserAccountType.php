<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserAccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $form = $event->getForm();
                $user = $event->getData();
                $form->add(
                    'picture',
                    HiddenType::class,
                    [
                        'mapped' => false,
                        'data' => $user->getPicture(),
                    ]
                );
            })
            ->add('username', TextType::class, [
                'label' => 'users.username.label',
                'attr' => [
                    'placeholder' => 'users.username.placeholder',
                ],
                'disabled' => true,
            ])
            ->add('plainPassword', RepeatedPasswordType::class)
            ->add('name', TextType::class, [
                'label' => 'users.name.label',
                'attr' => [
                    'placeholder' => 'users.name.placeholder',
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'users.last_name.label',
                'attr' => [
                    'placeholder' => 'users.last_name.placeholder',
                ],
            ])
            ->add('nick', TextType::class, [
                'label' => 'users.nick.label',
                'attr' => [
                    'placeholder' => 'users.nick.placeholder',
                ],
            ])
            ->add('email', TextType::class, [
                'label' => 'users.email.label',
                'attr' => [
                    'placeholder' => 'users.email.placeholder',
                ],
            ])
            ->add('locale', LanguageType::class, [
                'label' => 'users.locale.label',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'common.update',
                'attr' => ['class' => 'btn btn-square flat-btn btn-blue js-button'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'profile';
    }
}
