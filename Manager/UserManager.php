<?php

namespace Bigfoot\Bundle\UserBundle\Manager;

use Bigfoot\Bundle\ContextBundle\Service\ContextService as Context;
use Bigfoot\Bundle\CoreBundle\Generator\TokenGenerator;
use Bigfoot\Bundle\UserBundle\Entity\User;
use Bigfoot\Bundle\UserBundle\Mailer\UserMailer;
use Bigfoot\Bundle\UserBundle\Model\User as ModelUser;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;

/**
 * UserManager
 */
class UserManager
{
    protected $entityManager;
    protected $encoderFactory;
    protected $userChecker;
    protected $securityTokenStorage;
    protected $session;
    protected $context;
    protected $userMailer;
    protected $tokenGenerator;
    protected $requestStack;

    public function __construct(
        EntityManager $entityManager,
        EncoderFactoryInterface $encoderFactory,
        UserCheckerInterface $userChecker,
        TokenStorage $securityTokenStorage,
        SessionInterface $session,
        Context $context,
        UserMailer $userMailer,
        TokenGenerator $tokenGenerator
    ) {
        $this->entityManager        = $entityManager;
        $this->encoderFactory       = $encoderFactory;
        $this->userChecker          = $userChecker;
        $this->securityTokenStorage = $securityTokenStorage;
        $this->session              = $session;
        $this->context              = $context;
        $this->userMailer           = $userMailer;
        $this->tokenGenerator       = $tokenGenerator;
    }

    public function setRequestStack(RequestStack $requestStack = null)
    {
        $this->requestStack = $requestStack->getCurrentRequest();
    }

    public function createUser()
    {
        return new User();
    }

    public function loginUser($firewallName, ModelUser $user)
    {
        $this->userChecker->checkPostAuth($user);

        $token = $this->createToken($firewallName, $user);

        $this->securityTokenStorage->setToken($token);
    }

    public function updateUser(ModelUser $user)
    {
        $this->applyLocale($user);
        $this->updatePassword($user);
        $this->refreshUser($user);
        $this->entityManager->persist($user);
        $this->entityManager->flush($user);
    }

    public function refreshUser(ModelUser $user)
    {
        $context = $this->entityManager->getRepository('BigfootContextBundle:Context')->findOneByEntityIdEntityClass($user->getId(), 'Bigfoot\Bundle\UserBundle\Entity\User');

        if ($context) {
            $this->session->set('bigfoot/context/allowed_contexts', $context->getContextValues());
        }
    }

    public function updatePassword(ModelUser $user)
    {
        if (0 !== strlen($password = $user->getPlainPassword())) {
            $encoder = $this->getEncoder($user);
            $user->setPassword($encoder->encodePassword($password, $user->getSalt()));
            $user->eraseCredentials();
        }
    }

    public function applyLocale($user)
    {
        if ($user && $user instanceof ModelUser && $user->getLocale()) {
            $this->requestStack->getCurrentRequest()->getSession()->set('_locale', $user->getLocale());
        }
    }

    public function generateToken(ModelUser $user)
    {
        $token  = $this->tokenGenerator->generateToken();
        $status = true;

        try {
            $this->userMailer->sendForgotPasswordMail($user, $token);
        } catch (Exception $e) {
            $status  = false;
            $message = "Couldn't send email, please contact an admin";
        }

        if ($status == true) {
            $message = "Email sent check your inbox";

            $user->setConfirmationToken($token);
            $user->setPasswordRequestedAt(new \DateTime());

            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        return array(
            'status'  => $status,
            'message' => $message,
        );
    }

    public function createPassword(ModelUser $user)
    {
        $token  = $this->tokenGenerator->generateToken();
        $status = true;

        try {
            $this->userMailer->sendCreatePasswordMail($user, $token);
        } catch (Exception $e) {
            $status  = false;
            $message = "Couldn't send email, please contact an admin";
        }

        if ($status == true) {
            $message = "Email sent check your inbox";

            $user->setConfirmationToken($token);
            $user->setPasswordRequestedAt(new \DateTime());

            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        return array(
            'status'  => $status,
            'message' => $message,
        );
    }

    protected function getEncoder(ModelUser $user)
    {
        return $this->encoderFactory->getEncoder($user);
    }

    protected function createToken($firewall, ModelUser $user)
    {
        return new UsernamePasswordToken($user, null, $firewall, $user->getRoles());
    }
}
