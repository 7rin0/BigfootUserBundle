<?php

namespace Bigfoot\Bundle\UserBundle\Subscriber;

use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

use Bigfoot\Bundle\CoreBundle\Event\SettingsEvent;

/**
 * Settings Subscriber
 */
class SettingsSubscriber implements EventSubscriberInterface
{
    /**
     * Get subscribed events
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            SettingsEvent::GENERATE => array('onGenerate', 1)
        );
    }

    /**
     * @param GenericEvent $event
     */
    public function onGenerate(GenericEvent $event)
    {
        $builder = $event->getSubject();

        $builder
            ->add(
                'user_send_email',
                'checkbox',
                array(
                    'label'    => 'bigfoot_user.settings.label.send_email',
                    'required' => false
                )
            );
    }
}