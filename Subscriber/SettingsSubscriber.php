<?php

namespace Bigfoot\Bundle\UserBundle\Subscriber;

use Bigfoot\Bundle\CoreBundle\Event\SettingsEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

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
                CheckboxType::class,
                array(
                    'label'    => 'bigfoot_user.settings.label.send_email',
                    'required' => false
                )
            );
    }
}
