<?php

namespace Bigfoot\Bundle\UserBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Doctrine\ORM\EntityRepository;

/**
 * BigfootUserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BigfootUserRepository extends EntityRepository implements UserProviderInterface
{
    public function loadUserByUsername($username)
    {
        return $this
            ->createQueryBuilder('u')
            ->where('u.username = :username')
            ->orWhere('u.email = :email')
            ->setParameter('username', $username)
            ->setParameter('email', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function refreshUser(UserInterface $user)
    {
        return $this->findOneByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return in_array($class, array('Bigfoot\Bundle\UserBundle\Model\BigfootUser', 'Bigfoot\Bundle\UserBundle\Entity\BigfootUser'));
    }
}
