<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SearchListType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * SearchType constructor.
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'label' => false,
                    'attr' => [
                        'label' => $this->translator->trans('common.search'),
                        'placeholder' => $this->translator->trans('common.filter'),
                    ],
                ]
            );
    }
}
