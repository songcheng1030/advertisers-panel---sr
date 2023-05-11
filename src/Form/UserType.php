<?php

namespace App\Form;

use App\Entity\Campaign;
use App\Entity\User;
use App\Manager\UserManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserType extends AbstractType
{
    /**
     * @var UserManager
     */
    private $userManager;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * UserType constructor.
     */
    public function __construct(
        UserManager $userManager,
        TranslatorInterface $translator,
        TokenStorageInterface $tokenStorage
    ) {
        $this->userManager = $userManager;
        $this->tokenStorage = $tokenStorage;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $loggedUser = $this->tokenStorage->getToken()->getUser();

        if ($loggedUser->canCreateAccounts()) {
            if ($loggedUser->isAdmin()) {
                $this->buildFormForAdmin($builder, $options['is_edit']);
            }

            if ($loggedUser->isSalesManagerHead()) {
                $this->buildFormForSalesManagerHead($builder, $options['is_edit']);
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_edit' => false,
        ]);

        $resolver->setAllowedTypes('is_edit', 'bool');
    }

    private function buildFormForAdmin(FormBuilderInterface $builder, bool $isEdit = false)
    {
        $builder
            ->add('roles', RoleType::class)
            ->add('salesManagerHead', EntityType::class, [
                'class' => User::class,
                'choices' => $this->userManager->getSalesManagerHeads(),
                'choice_label' => 'fullName',
            ])
            ->add('campaigns', CampaignsType::class, [
                'class' => Campaign::class,
                'choice_value' => 'id',
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'label' => 'campaigns',
            ])
            ->add('username', TextType::class, [
                'label' => 'users.username.label',
                'attr' => [
                    'placeholder' => 'users.username.placeholder',
                ],
                'disabled' => $isEdit,
            ])
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
                'label' => $isEdit ? 'common.update' : 'common.save_changes',
                'attr' => ['class' => 'btn btn-square flat-btn btn-blue js-button'],
            ]);

        if ($isEdit) {
            $builder
                ->add('plainPassword', RepeatedPasswordType::class)
                ->add('isEmailNotificationEnabled', CheckboxType::class, [
                    'label' => 'users.is_email_notification_enabled.label',
                    'attr' => [
                        'class' => 'show-checkbox',
                    ],
                ]);
        } else {
            $builder
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
                ])
                ->add('isEmailNotificationEnabled', CheckboxType::class, [
                    'label' => 'users.is_email_notification_enabled.label',
                    'data' => true,
                    'attr' => [
                        'class' => 'show-checkbox',
                    ],
                ]);
        }
    }

    private function buildFormForSalesManagerHead(FormBuilderInterface $builder, bool $isEdit = false)
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'users.username.label',
                'attr' => [
                    'placeholder' => 'users.username.placeholder',
                ],
                'disabled' => $isEdit,
            ])
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
                'label' => $isEdit ? 'common.update' : 'common.save_changes',
                'attr' => ['class' => 'btn btn-square flat-btn btn-blue js-button'],
            ]);

        if ($isEdit) {
            $builder
                ->add('plainPassword', RepeatedPasswordType::class)
                ->add('isEmailNotificationEnabled', CheckboxType::class, [
                    'label' => 'users.is_email_notification_enabled.label',
                    'attr' => [
                        'class' => 'show-checkbox',
                    ],
                ]);
        } else {
            $builder
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
                ])
                ->add('isEmailNotificationEnabled', CheckboxType::class, [
                    'label' => 'users.is_email_notification_enabled.label',
                    'data' => true,
                    'attr' => [
                        'class' => 'show-checkbox',
                    ],
                ]);
        }
    }
}
