<?php

namespace App\Form;

use App\Entity\Agency;
use App\Entity\Country;
use App\Entity\User;
use App\Manager\UserManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AgencyType extends AbstractType
{
    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * AgencyType constructor.
     */
    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'common.name.label',
                'attr' => [
                    'placeholder' => 'common.name.placeholder',
                ],
            ])
            ->add('type', AgencyFieldType::class, [
                'data' => AgencyFieldType::TYPE_AGENCY,
            ])
            ->add('salesManager', EntityType::class, [
                'class' => User::class,
                'choices' => $this->userManager->getSalesManagers(),
                'choice_label' => 'fullName',
            ])
            ->add('rebate', IntegerType::class, [
                'label' => 'common.rebate.label',
                'attr' => [
                    'placeholder' => 'common.rebate.placeholder',
                ],
            ])
            ->add('countries', CountriesType::class, [
                'class' => Country::class,
                'choice_value' => 'id',
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('billingFiscalState', FiscalStateType::class, [
                'label' => 'agencies.fiscal_state.label',
            ])
            ->add('billingNifCif', TextType::class, [
                'label' => 'agencies.nif_cif.label',
                'attr' => [
                    'placeholder' => 'agencies.nif_cif.placeholder',
                ],
            ])
            ->add('billingCompany', TextType::class, [
                'label' => 'agencies.company.label',
                'attr' => [
                    'placeholder' => 'agencies.company.placeholder',
                ],
            ])
            ->add('billingCountry', EntityType::class, [
                'label' => 'agencies.country.label',
                'class' => Country::class,
                'choice_value' => 'id',
                'choice_label' => 'name',
            ])
            ->add('billingCity', TextType::class, [
                'label' => 'agencies.city.label',
                'attr' => [
                    'placeholder' => 'agencies.city.placeholder',
                ],
            ])
            ->add('billingProvince', TextType::class, [
                'label' => 'agencies.province.label',
                'attr' => [
                    'placeholder' => 'agencies.province.placeholder',
                ],
            ])
            ->add('billingCp', TextType::class, [
                'label' => 'agencies.cp.label',
                'attr' => [
                    'placeholder' => 'agencies.cp.placeholder',
                ],
            ])
            ->add('billingAddress', TextType::class, [
                'label' => 'agencies.address.label',
                'attr' => [
                    'placeholder' => 'agencies.address.placeholder',
                ],
            ])
            ->add('details', TextareaType::class, [
                'label' => 'agencies.details.label',
                'attr' => [
                    'placeholder' => 'agencies.details.placeholder',
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => $options['is_edit'] ? 'common.update' : 'common.save_changes',
                'attr' => ['class' => 'btn btn-square flat-btn btn-blue js-button'],
            ]);

        if ($options['is_edit']) {
            $builder
                ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                    $agency = $event->getData();
                    $form = $event->getForm();

                    $managers = $agency->getAccountManager();
                    $firstManager = array_shift($managers);

                    $form
                        ->add(
                            'accountManagerName',
                            TextType::class,
                            [
                                'label' => 'common.name.label',
                                'required' => false,
                                'mapped' => false,
                                'data' => $firstManager['name'],
                                'attr' => [
                                    'placeholder' => 'common.name.placeholder',
                                ],
                            ]
                        )
                        ->add(
                            'accountManagerEmail',
                            EmailType::class,
                            [
                                'label' => 'common.email.label',
                                'required' => false,
                                'mapped' => false,
                                'data' => $firstManager['email'],
                                'attr' => [
                                    'placeholder' => 'common.email.placeholder',
                                ],
                            ]
                        )
                        ->add(
                            'accountManagerPhone',
                            TextType::class,
                            [
                                'label' => 'common.phone.label',
                                'required' => false,
                                'mapped' => false,
                                'data' => $firstManager['phone'],
                                'attr' => [
                                    'placeholder' => 'common.phone.placeholder',
                                ],
                            ]
                        )
                        ->add('account_manager', CollectionType::class, [
                            'allow_add' => true,
                            'allow_delete' => true,
                            'prototype' => false,
                            'by_reference' => false,
                            'entry_type' => AccountManagerType::class,
                            'label' => false,
                            'mapped' => false,
                            'data' => $managers,
                        ]);
                });
        } else {
            $builder
                ->add(
                    'accountManagerName',
                    TextType::class,
                    [
                        'label' => 'common.name.label',
                        'required' => false,
                        'mapped' => false,
                        'attr' => [
                            'placeholder' => 'common.name.placeholder',
                        ],
                    ]
                )
                ->add(
                    'accountManagerEmail',
                    EmailType::class,
                    [
                        'label' => 'common.email.label',
                        'required' => false,
                        'mapped' => false,
                        'attr' => [
                            'placeholder' => 'common.email.placeholder',
                        ],
                    ]
                )
                ->add(
                    'accountManagerPhone',
                    TextType::class,
                    [
                        'label' => 'common.phone.label',
                        'required' => false,
                        'mapped' => false,
                        'attr' => [
                            'placeholder' => 'common.phone.placeholder',
                        ],
                    ]
                )
                ->add('account_manager', CollectionType::class, [
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => false,
                    'by_reference' => false,
                    'entry_type' => AccountManagerType::class,
                    'label' => false,
                    'mapped' => false,
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Agency::class,
            'is_edit' => false,
            'allow_extra_fields' => true,
        ]);

        $resolver->setAllowedTypes('is_edit', 'bool');
    }
}
