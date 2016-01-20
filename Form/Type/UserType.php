<?php

namespace Bigfoot\Bundle\UserBundle\Form\Type;

use Bigfoot\Bundle\ContextBundle\Service\ContextService;
use Bigfoot\Bundle\UserBundle\Event\UserEvent;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

/**
 * User Type
 *
 * @package BigfootUserBundle
 */
class UserType extends AbstractType
{
    /**
     * @var AuthorizationChecker
     */
    private $authorizationChecker;

    /**
     * @var array
     */
    private $languages;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * Constructor
     *
     * @param AuthorizationChecker $securityContext
     * @param ContextService $context
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        AuthorizationChecker $securityContext,
        ContextService $context,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->authorizationChecker = $securityContext;
        $this->languages            = $context->getValues('language_back');
        $this->eventDispatcher      = $eventDispatcher;
    }

    /**
     * BuildForm
     *
     * @param  FormBuilderInterface $builder
     * @param  array $options
     *
     * @return null
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $langs       = array_keys($this->languages);
        $langChoices = array_combine($langs, $langs);

        $builder
            ->add('username')
            ->add(
                'email',
                TextType::class,
                array(
                    'attr' => array(
                        'class' => 'width-100'
                    )
                )
            )
            ->add('fullName')
            ->add(
                'locale',
                ChoiceType::class,
                array(
                    'choices' => $langChoices
                )
            );

        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $builder
                ->add(
                    'enabled',
                    CheckboxType::class,
                    array(
                        'required' => false
                    )
                )
                ->add(
                    'formRoles',
                    EntityType::class,
                    array(
                        'class'    => 'BigfootUserBundle:Role',
                        'multiple' => true,
                    )
                );
        }

        $builder
            ->add(
                'plainPassword',
                'repeated',
                array(
                    'type'     => 'password',
                    'required' => false,
                )
            );

        $this->eventDispatcher->dispatch(UserEvent::CREATE_FORM, new GenericEvent($builder));
    }

    /**
     * configureOptions
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Bigfoot\Bundle\UserBundle\Entity\User'
            )
        );
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return 'admin_user';
    }
}
