<?php

namespace Bigfoot\Bundle\UserBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoleMenuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'slug',
                HiddenType::class,
                array(
                    'label' => '',
                    'required' => false,
                )
            )
            ->add(
                'label',
                TextType::class,
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
                HiddenType::class,
                array(
                    'label' => '',
                    'required' => false,
                )
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Bigfoot\Bundle\UserBundle\Entity\RoleMenu'
            )
        );
    }
}
