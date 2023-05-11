<?php

namespace App\Form;

use App\Entity\Advertiser;
use App\Entity\Agency;
use App\Entity\Campaign;
use App\Entity\Country;
use App\Entity\Dsp;
use App\Entity\Ssp;
use App\Manager\AdvertiserManager;
use App\Manager\DspManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class CampaignType extends AbstractType
{
    const DECIMAL_AMOUNT_ALLOWED = 6;
    /**
     * @var TranslatorInterface
     */
    private $translator;
    /**
     * @var DspManager
     */
    private $dspManager;
    /**
     * @var AdvertiserManager
     */
    private $advertiserManager;

    /**
     * CampaignType constructor.
     */
    public function __construct(
        TranslatorInterface $translator,
        DspManager $dspManager,
        AdvertiserManager $advertiserManager
    ) {
        $this->translator = $translator;
        $this->dspManager = $dspManager;
        $this->advertiserManager = $advertiserManager;
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
            ->add('agency', AgenciesType::class, [
                'class' => Agency::class,
                'choice_label' => function ($agency) {
                    return $agency->getName() . ' (' . $agency->getSalesManager()->getFullName() . ')';
                },
                'label' => 'common.agency.label',
            ])
            ->add('advertiser', EntityType::class, [
                'class' => Advertiser::class,
                'choices' => $this->advertiserManager->getAdvertisers(),
                'choice_label' => 'name',
                'label' => 'common.advertiser.label',
            ])
            ->add('dealId', TextType::class, [
                'label' => 'common.deal_id.label',
                'attr' => [
                    'placeholder' => 'common.deal_id.placeholder',
                ],
            ])
            ->add('dsp', EntityType::class, [
                'class' => Dsp::class,
                'choices' => $this->dspManager->getDsps(),
                'choice_label' => 'name',
                'label' => 'common.dsp.label',
            ])
            ->add('vtr', IntegerType::class, [
                'label' => 'common.vtr.label',
                'attr' => [
                    'placeholder' => 'common.vtr.placeholder',
                    'class' => 'half',
                ],
            ])
            ->add('viewability', IntegerType::class, [
                'label' => 'common.viewability.label',
                'attr' => [
                    'placeholder' => 'common.viewability.placeholder',
                    'class' => 'half',
                ],
            ])
            ->add('ctr', IntegerType::class, [
                'label' => 'common.ctr.label',
                'attr' => [
                    'placeholder' => 'common.ctr.placeholder',
                    'class' => 'half',
                ],
            ])
            ->add('volume', IntegerType::class, [
                'label' => 'common.volume.label',
                'attr' => [
                    'placeholder' => 'common.volume.placeholder',
                    'class' => 'half',
                ],
            ])
            ->add('vtrFrom', IntegerType::class, [
                'label' => 'common.vtr.label',
                'attr' => [
                    'placeholder' => 'common.from',
                    'class' => 'half',
                ],
            ])
            ->add('vtrTo', IntegerType::class, [
                'label' => 'common.vtr.label',
                'attr' => [
                    'placeholder' => 'common.to',
                    'class' => 'half',
                ],
            ])
            ->add('viewabilityFrom', IntegerType::class, [
                'label' => 'common.viewability.label',
                'attr' => [
                    'placeholder' => 'common.from',
                    'class' => 'half',
                ],
            ])
            ->add('viewabilityTo', IntegerType::class, [
                'label' => 'common.viewability.label',
                'attr' => [
                    'placeholder' => 'common.to',
                    'class' => 'half',
                ],
            ])
            ->add('ctrFrom', IntegerType::class, [
                'label' => 'common.ctr.label',
                'attr' => [
                    'placeholder' => 'common.from',
                    'class' => 'half',
                ],
            ])
            ->add('ctrTo', IntegerType::class, [
                'label' => 'common.ctr.label',
                'attr' => [
                    'placeholder' => 'common.to',
                    'class' => 'half',
                ],
            ])
            ->add('listType', ListFieldType::class, [
                'label' => 'common.list_type.label',
            ])
            ->add('list', FileType::class, [
                'label' => 'common.list.label',
                'mapped' => false,
                'multiple' => true,
                'required' => false,
            ])
            ->add('details', TextareaType::class, [
                'label' => 'agencies.details.label',
                'attr' => [
                    'placeholder' => 'agencies.details.placeholder',
                ],
            ])
            ->add('countries', CountriesType::class, [
                'class' => Country::class,
                'choice_value' => 'id',
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('cpm', NumberType::class, [
                'label' => 'common.cpm.label',
                'html5' => true,
                'attr' => [
                    'placeholder' => 'common.cpm.placeholder',
                    'class' => 'half',
                ],
            ])
            ->add('cpv', NumberType::class, [
                'label' => 'common.cpv.label',
                'html5' => true,
                'attr' => [
                    'placeholder' => 'common.cpv.placeholder',
                    'class' => 'half',
                ],
                'scale' => self::DECIMAL_AMOUNT_ALLOWED,
            ])
            ->add('startAt', DateType::class, [
                'label' => 'common.start_at.label',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'half',
                ],
            ])
            ->add('endAt', DateType::class, [
                'label' => 'common.end_at.label',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'half',
                ],
            ])
            ->add('rebate', IntegerType::class, [
                'label' => 'common.rebate.label',
                'attr' => [
                    'placeholder' => 'common.rebate.placeholder',
                    'class' => 'half',
                ],
            ])
            ->add('status', CampaignStatusFieldType::class, [
                'label' => 'common.status.label',
                'data' => CampaignStatusFieldType::STATUS_ACTIVE,
            ])
            ->add('save', SubmitType::class, [
                'label' => (!$options['is_edit'] || $options['is_clone']) ? 'common.save_changes' : 'common.update',
                'attr' => ['class' => 'btn btn-square flat-btn btn-blue js-button'],
            ]);

        if ($options['is_edit']) {
            $builder
                ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
                    $form = $event->getForm();
                    $campaign = $event->getData();

                    $form
                        ->add('costType', CostFieldType::class, [
                            'label' => 'common.cost_type.label',
                            'data' => $campaign->getCpm() ? CostFieldType::TYPE_CPM : CostFieldType::TYPE_CPV,
                            'mapped' => false,
                        ])
                        ->add('ssp', TextType::class, [
                            'label' => 'common.ssp.label',
                            'data' => $campaign->getSsp()->getName(),
                            'disabled' => true,
                        ])
                        ->add('type', TextType::class, [
                            'label' => 'common.type.label',
                            'data' => $this->translator->trans(CampaignFieldType::$typesNames[$campaign->getType()]),
                            'disabled' => true,
                        ])
                        ->add('typeId', HiddenType::class, [
                            'label' => 'common.type.label',
                            'mapped' => false,
                            'disabled' => true,
                            'data' => $campaign->getType(),
                        ]);
                });
        } else {
            $builder
                ->add('costType', CostFieldType::class, [
                    'label' => 'common.cost_type.label',
                    'data' => CostFieldType::TYPE_CPM,
                    'mapped' => false,
                ])
                ->add('ssp', EntityType::class, [
                    'class' => Ssp::class,
                    'choice_label' => 'name',
                    'label' => 'common.ssp.label',
                ])
                ->add('type', CampaignFieldType::class, [
                    'label' => 'common.type.label',
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Campaign::class,
            'is_edit' => false,
            'is_clone' => false,
        ]);

        $resolver->setAllowedTypes('is_edit', 'bool');
        $resolver->setAllowedTypes('is_clone', 'bool');
    }
}
