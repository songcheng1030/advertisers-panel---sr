<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoleType extends AbstractType
{
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_SALES_MANAGER = 'ROLE_SALES_MANAGER';
    const ROLE_SALES_MANAGER_HEAD = 'ROLE_SALES_MANAGER_HEAD';
    const ROLE_CAMPAIGN_VIEWER = 'ROLE_CAMPAIGN_VIEWER';

    public function configureOptions(OptionsResolver $resolver)
    {
        $roleTypes = [
            self::ROLE_ADMIN => 'users.role.admin',
            self::ROLE_SALES_MANAGER => 'users.role.sales_manager',
            self::ROLE_SALES_MANAGER_HEAD => 'users.role.sales_manager_head',
            self::ROLE_CAMPAIGN_VIEWER => 'users.role.campaign_viewer',
        ];

        $resolver->setDefaults([
            'multiple' => true,
            'choices' => array_flip($roleTypes),
            'choice_translation_domain' => true,
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
