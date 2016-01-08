<?php

namespace Bigfoot\Bundle\UserBundle\DataFixtures\ORM;

use Bigfoot\Bundle\UserBundle\Entity\Role;
use Bigfoot\Bundle\UserBundle\Entity\Translation\RoleTranslation;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadRoleData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $roleAdmin = new Role();
        $roleAdmin
            ->setName('ROLE_ADMIN')
            ->setLabel('Admin');

        $this->setReference('ROLE_ADMIN', $roleAdmin);

        $roleUser = new Role();
        $roleUser
            ->setName('ROLE_USER')
            ->setLabel('User');

        $this->setReference('ROLE_USER', $roleUser);

        $roleAdmin->addTranslation(new RoleTranslation('fr', 'label', 'Administrateur'));
        $roleUser->addTranslation(new RoleTranslation('fr', 'label', 'Utilisateur'));

        $manager->persist($roleAdmin);
        $manager->persist($roleUser);
        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}
