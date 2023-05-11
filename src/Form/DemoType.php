<?php

namespace App\Form;

use App\Entity\Demo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DemoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $isEdit = $options['is_edit'];
        $options =
            [
                'label' => 'demo.url_format.label',
                'attr' => [
                    'placeholder' => 'demo.url_format.placeholder',
                    'class' => 'half',
                ],
                'disabled' => $isEdit,
            ];
        if (!$isEdit) {
            $options['choices'] =
                [
                    'Demo' => 'demo',
                    'Enhancedvideo' => 'enhancedvideo',
                ];
        }

        $builder
            ->add('url_format', $isEdit ? TextType::class : ChoiceType::class, $options)
            ->add('url', TextType::class, [
                'label' => 'demo.url.label',
                'attr' => [
                    'placeholder' => 'demo.url.placeholder',
                    'class' => 'half',
                ],
                'required' => true,
                'disabled' => $isEdit,
            ])
            ->add('format', ChoiceType::class, [
                'label' => 'demo.format.label',
                'choices' => [
                    'Intext' => 'intext',
                    'Slider' => 'slider',
                ],
                'attr' => [
                    'placeholder' => 'demo.format.placeholder',
                    'class' => 'half',
                ],
            ])
            ->add('video', TextType::class, [
                'label' => 'demo.video.label',
                'attr' => [
                    'placeholder' => 'demo.video.placeholder',
                    'class' => 'hidden',
                ],
            ])
            // ->add('supply_source_desktop', TextType::class, [
            // 'label' => 'demo.supply_source_desktop.label',
            // 'attr' => [
            // 'placeholder' => 'demo.supply_source_desktop.placeholder',
            // 'class' => 'half',
            // ],
            // ])
            // ->add('supply_source_mobile', TextType::class, [
            // 'label' => 'demo.supply_source_mobile.label',
            // 'attr' => [
            // 'placeholder' => 'demo.supply_source_mobile.placeholder',
            // 'class' => 'half',
            // ],
            // ])
            ->add('desktop', CheckboxType::class, [
                'label' => 'demo.desktop',
                'required' => false,
            ])

            ->add('width', IntegerType::class, [
                'label' => 'WIDTH',
                'attr' => [
                    'placeholder' => 'demo.width.placeholder',
                    'class' => 'half',
                ],
            ])
            ->add('height', IntegerType::class, [
                'label' => 'HEIGHT',
                'attr' => [
                    'placeholder' => 'demo.height.placeholder',
                    'class' => 'half',
                ],
            ])

            ->add('mobile', CheckboxType::class, [
                'label' => 'demo.mobile',
                'required' => false,
            ])
            ->add('orientation', ChoiceType::class, [
                'label' => 'demo.orientation.label',
                'choices' => [
                    'Landscape' => 'landscape',
                    'Portrait' => 'portrait',
                ],
                'attr' => [
                    'placeholder' => 'demo.orientation.placeholder',
                    'class' => 'half',
                ],
            ])
            // ->add('width_mobile', IntegerType::class, [
            // 'label' => 'WIDTH',
            // 'attr' => [
            // 'placeholder' => 'demo.width.placeholder',
            // 'class' => 'half',
            // ],
            // ])
            // ->add('height_mobile', IntegerType::class, [
            // 'label' => 'HEIGHT',
            // 'attr' => [
            // 'placeholder' => 'demo.height.placeholder',
            // 'class' => 'half',
            // ],
            // ])

            // ->add('template', ChoiceType::class, [
            // 'label' => 'demo.template.label',
            // 'choices' => array_merge(array(
            // 'Default' => 'default',
            // ),((@$templates)?$templates:[])),
            // 'attr' => [
            // 'placeholder' => 'demo.template.placeholder',
            // 'class' => 'half',
            // ],
            // 'disabled' => $isEdit,
            // ])
            // ->add('date', DateType::class, [
            // 'label' => 'demo.date.label',
            // 'widget' => 'single_text',
            // 'attr' => [
            // 'placeholder' => 'demo.date.placeholder',
            // 'class' => 'half',
            // ],
            // 'disabled' => $isEdit,
            // ])
            ->add('click_url', TextType::class, [
                'label' => 'click url',
                'attr' => [
                    'placeholder' => 'demo.url.placeholder',
                    'class' => 'half',
                ],
                'required' => false,
            ])
            ->add('save', SubmitType::class, [
                'label' => $isEdit ? 'common.update' : 'common.save_changes',
                'attr' => ['class' => 'btn btn-square flat-btn btn-blue js-button'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Demo::class,
            'is_edit' => false,
        ]);

        $resolver->setAllowedTypes('is_edit', 'bool');
    }
}
