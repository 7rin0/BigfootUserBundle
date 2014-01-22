<?php

namespace Bigfoot\Bundle\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityManager;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'plainPassword',
                'repeated',
                array(
                    'type'            => 'password',
                    'invalid_message' => 'Passwords are not identical.',
                    'required'        => true,
                )
            );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Bigfoot\Bundle\UserBundle\Form\Model\ResetPasswordModel',
            )
        );
    }

    public function getName()
    {
        return 'admin_reset_password';
    }
}