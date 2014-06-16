<?php

namespace Bigfoot\Bundle\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RoleMenuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'slug',
                'hidden',
                array(
                    'label' => '',
                    'required' => false,
                )
            )
            ->add(
                'label',
                'text',
                array(
                    'disabled' => true,
                    'label' => '',
                    'required' => false,
                )
            )
            ->add(
                'roles',
                'entity',
                array(
                    'class'    => 'BigfootUserBundle:Role',
                    'property' => 'label',
                    'multiple' => true,
                    'label' => '',
                    'required' => false,
                )
            )
            ->add(
                'level',
                'hidden',
                array(
                    'label' => '',
                    'required' => false,
                )
            );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Bigfoot\Bundle\UserBundle\Entity\RoleMenu'
            )
        );
    }

    public function getName()
    {
        return 'role_menu_type';
    }
}
