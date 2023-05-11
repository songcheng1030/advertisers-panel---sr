<?php

namespace App\Form;

use App\Entity\Agency;
use App\Entity\Country;
use App\Entity\User;
use App\Manager\UserManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AgencyAjaxType extends AbstractType
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
     * AgencyAjaxType constructor.
     */
    public function __construct(UserManager $userManager, TokenStorageInterface $tokenStorage)
    {
        $this->userManager = $userManager;
        $this->tokenStorage = $tokenStorage;
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
            ->add('save', ButtonType::class, [
                'label' => 'common.save_changes',
                'attr' => ['class' => 'btn btn-square flat-btn btn-blue',
                            'id' => 'submit_form', ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Agency::class,
        ]);
    }
}
