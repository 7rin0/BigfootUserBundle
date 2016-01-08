<?php

namespace Bigfoot\Bundle\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    public $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $userManager = $this->container->get('bigfoot_user.manager.user');
        $roleAdmin   = $this->getReference('ROLE_ADMIN');

        $admin = $userManager
            ->createUser()
            ->setUsername('admin')
            ->setPlainPassword('admin')
            ->setFullname('Administrator')
            ->setEmail('admin@c2is.fr')
            ->setLocale('en')
            ->addRole($roleAdmin);
        $userManager->updatePassword($admin);

        $admin2 = $userManager
            ->createUser()
            ->setUsername('testAdmin')
            ->setPlainPassword('testAdmin')
            ->setFullname('testAdministrator')
            ->setEmail('testAdmin@c2is.fr')
            ->setLocale('en');
        $userManager->updatePassword($admin2);

        $manager->persist($admin);
        $manager->persist($admin2);

        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }
}