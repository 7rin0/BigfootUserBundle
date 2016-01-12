<?php

namespace Bigfoot\Bundle\UserBundle\Form\Type;

use Bigfoot\Bundle\UserBundle\Form\DataTransformer\StringToUserTransformer;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ForgotPasswordType extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'email',
                EmailType::class,
                array(
                    'invalid_message' => 'Invalid email',
                    'required'        => true,
                )
            );

        $builder
            ->get('email')
            ->addModelTransformer(new StringToUserTransformer($this->entityManager));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class'      => 'Bigfoot\Bundle\UserBundle\Form\Model\ForgotPasswordModel',
                'csrf_protection' => false,
            )
        );
    }

    public function getName()
    {
        return 'admin_forgot_password';
    }
}
