<?php

namespace Bigfoot\Bundle\UserBundle\Subscriber;

use Bigfoot\Bundle\UserBundle\Entity\User;
use Bigfoot\Bundle\UserBundle\Event\UserEvent;
use Bigfoot\Bundle\UserBundle\Manager\UserManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

/**
 * User Subscriber
 */
class UserSubscriber implements EventSubscriberInterface
{
    private $userManager;

    /**
     * Construct UserSubscriber
     */
    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * Get subscribed events
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            SecurityEvents::INTERACTIVE_LOGIN => 'onSecurityInteractiveLogin',
            UserEvent::RESET_PASSWORD         => 'onResetPassword',
            UserEvent::UPDATE_PROFILE         => 'onUpdateProfile',
            UserEvent::UPDATE_PASSWORD        => 'onUpdatePassword',
            UserEvent::REFRESH_USER           => 'onRefreshUser',
        );
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();

        if ($user instanceof User) {
            $user->setLastLogin(new \DateTime());
            $this->userManager->updateUser($user);
        }
    }

    /**
     * Reset password
     */
    public function onResetPassword(GenericEvent $event)
    {
        $user = $event->getSubject();

        $user->setConfirmationToken(null);
        $user->setPasswordRequestedAt(null);

        $this->userManager->updateUser($user);
        $this->userManager->loginUser('back_office', $user);
    }

    /**
     * Executed after profile update
     */
    public function onUpdateProfile(GenericEvent $event)
    {
        $user = $event->getSubject();

        $this->userManager->applyLocale($user);
        $this->userManager->updatePassword($user);
    }

    /**
     * Executed after flush
     */
    public function onRefreshUser(GenericEvent $event)
    {
        $user = $event->getSubject();

        $this->userManager->refreshUser($user);
    }
}
