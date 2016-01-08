<?php

namespace Bigfoot\Bundle\UserBundle\Subscriber;

use Bigfoot\Bundle\CoreBundle\Event\MenuEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Menu Subscriber
 */
class MenuSubscriber implements EventSubscriberInterface
{
    /**
     * @var TokenStorage
     */
    private $security;

    /**
     * @param TokenStorage $security
     */
    public function __construct(TokenStorage $security)
    {
        $this->security = $security;
    }

    /**
     * Get subscribed events
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            MenuEvent::GENERATE_MAIN => array('onGenerateMain', 1)
        );
    }

    /**
     * @param GenericEvent $event
     */
    public function onGenerateMain(GenericEvent $event)
    {
        $builder = $event->getSubject();

        $builder
            ->addChildFor(
                'settings',
                'settings_role_menu',
                array(
                    'label'  => 'Role Menu',
                    'route'  => 'admin_role_menu',
                    'linkAttributes' => array(
                        'icon' => 'wrench',
                    )
                )
            )
            ->addChild(
                'users',
                array(
                    'label'          => 'Users',
                    'url'            => '#',
                    'attributes' => array(
                        'class' => 'parent',
                    ),
                    'linkAttributes' => array(
                        'class' => 'dropdown-toggle',
                        'icon'  => 'group',
                    )
                ),
                array(
                    'children-attributes' => array(
                        'class' => 'submenu'
                    )
                )
            )
            ->addChildFor(
                'users',
                'users_user',
                array(
                    'label'  => 'User',
                    'route'  => 'admin_user',
                    'extras' => array(
                        'routes' => array(
                            'admin_user_new',
                            'admin_user_edit'
                        )
                    ),
                    'linkAttributes' => array(
                        'icon' => 'user',
                    )
                )
            )
            ->addChildFor(
                'users',
                'users_role',
                array(
                    'label'  => 'Role',
                    'route'  => 'admin_role',
                    'extras' => array(
                        'routes' => array(
                            'admin_role_new',
                            'admin_role_edit'
                        )
                    ),
                    'linkAttributes' => array(
                        'icon' => 'legal',
                    )
                )
            );
    }
}
