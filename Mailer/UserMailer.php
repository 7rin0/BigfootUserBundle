<?php

namespace Bigfoot\Bundle\UserBundle\Mailer;

use Bigfoot\Bundle\CoreBundle\Mailer\AbstractMailer;
use Bigfoot\Bundle\UserBundle\Event\UserEvent;
use Bigfoot\Bundle\UserBundle\Model\User;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class UserMailer extends AbstractMailer
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * Constructor
     *
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * sendForgotPasswordMail
     *
     * @param  User   $user
     * @param  string $token
     * @return null
     */
    public function sendForgotPasswordMail(User $user, $token)
    {
        $body = $this->getTemplating()->render(
            'BigfootUserBundle:Mail:forgot_password.html.twig',
            array(
                'token' => $token,
            )
        );

        $this->sendMail(
            'Forgot password',
            $user->getEmail(),
            $body
        );
    }

    /**
     * sendCreatePasswordMail
     *
     * @param  User   $user
     * @param  string $token
     * @return boolean
     */
    public function sendCreatePasswordMail(User $user, $token)
    {
        $event = new GenericEvent(
            array(
                'user'  => $user,
                'token' => $token
            )
        );

        $this->eventDispatcher->dispatch(UserEvent::CREATE_PASSWORD, $event);

        if (!$event->isPropagationStopped()) {
            $body = $this->getTemplating()->render(
                'BigfootUserBundle:Mail:forgot_password.html.twig',
                array(
                    'token' => $token,
                )
            );

            $this->sendMail(
                'Forgot password',
                $user->getEmail(),
                $body
            );
        }
    }
}
