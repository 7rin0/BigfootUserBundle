<?php

namespace Bigfoot\Bundle\UserBundle\Listener;

use Bigfoot\Bundle\UserBundle\Model\User;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Gedmo\Translatable\TranslatableListener;

/**
 * Class KernelListener
 * @package Bigfoot\Bundle\UserBundle\Listener
 */
class KernelListener
{
    /** @var TokenStorage */
    protected $securityTokenStorage;

    /** @var Kernel */
    protected $kernel;

    /**
     * @param TokenStorage $securityContext
     * @param Kernel $kernel
     */
    public function __construct(TokenStorage $securityContext, Kernel $kernel)
    {
        $this->securityTokenStorage = $securityContext;
        $this->kernel = $kernel;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType() or !in_array($this->kernel->getEnvironment(), array('admin', 'admin_dev'))) {
            return;
        }

        $request = $event->getRequest();
        $token   = $this->securityTokenStorage->getToken();

        if ($token and $user = $token->getUser() and $user instanceof User) {
            $request->setLocale($user->getLocale());
        }
    }
}
