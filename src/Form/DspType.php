<?php

namespace App\Form;

use App\Entity\Dsp;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DspType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('save', SubmitType::class, [
                'label' => $options['is_edit'] ? 'common.update' : 'common.save_changes',
                'attr' => ['class' => 'btn btn-square flat-btn btn-blue js-button'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Dsp::class,
            'is_edit' => false,
        ]);

        $resolver->setAllowedTypes('is_edit', 'bool');
    }
}
