<?php

namespace App\Form;

use App\Entity\Target;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class TargetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('goal', IntegerType::class, [
                'label' => 'common.goal',
                'attr' => [
                    'class' => 'half',
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'common.field_must_be_completed',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Target::class,
        ]);
    }
}
