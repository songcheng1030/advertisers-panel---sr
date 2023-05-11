<?php

namespace App\Form;

use App\Entity\Advertiser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AdvertiserType extends AbstractType
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
            ->add('plainPassword', RepeatedPasswordType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'common.field_must_be_completed',
                    ]),
                    new Length([
                        'min' => 8,
                        'max' => 20,
                        'minMessage' => 'common.field_min_message',
                        'maxMessage' => 'common.field_max_message',
                    ]),
                ],
                'mapped' => false,
            ])
            ->add('save', SubmitType::class, [
                'label' => $options['is_edit'] ? 'common.update' : 'common.save_changes',
                'attr' => ['class' => 'btn btn-square flat-btn btn-blue js-button'],
            ]);

        if ($options['is_edit']) {
            $builder
                ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                    $email = '';
                    $username = '';
                    $locale = '';
                    $form = $event->getForm();
                    $advertiser = $event->getData();
                    $user = $advertiser->getUser();

                    if ($user) {
                        $email = $user->getEmail();
                        $username = $user->getUsername();
                        $locale = $user->getLocale();
                    }

                    $form
                        ->add('email', EmailType::class, [
                            'label' => 'users.email.label',
                            'attr' => [
                                'placeholder' => 'users.email.placeholder',
                            ],
                            'mapped' => false,
                            'data' => $email,
                        ])
                        ->add('username', TextType::class, [
                            'label' => 'users.username.label',
                            'attr' => [
                                'placeholder' => 'users.username.placeholder',
                            ],
                            'mapped' => false,
                            'data' => $username,
                        ])
                        ->add('locale', LocaleFieldType::class, [
                            'label' => 'users.locale.label',
                            'mapped' => false,
                            'data' => $locale,
                        ]);

                    if (!$user) {
                        $form
                            ->add('createUser', CheckboxType::class, [
                                'mapped' => false,
                            ]);
                    }
                });
        } else {
            $builder
                ->add('createUser', CheckboxType::class, [
                    'mapped' => false,
                    'data' => $options['has_user'],
                    'disabled' => $options['has_user'],
                ])
                ->add('email', EmailType::class, [
                    'label' => 'users.email.label',
                    'attr' => [
                        'placeholder' => 'users.email.placeholder',
                    ],
                    'mapped' => false,
                ])
                ->add('username', TextType::class, [
                    'label' => 'users.username.label',
                    'attr' => [
                        'placeholder' => 'users.username.placeholder',
                    ],
                    'mapped' => false,
                ])
                ->add('locale', LocaleFieldType::class, [
                    'label' => 'users.locale.label',
                    'mapped' => false,
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Advertiser::class,
            'is_edit' => false,
            'has_user' => false,
        ]);

        $resolver->setAllowedTypes('is_edit', 'bool');
        $resolver->setAllowedTypes('has_user', 'bool');
    }
}
