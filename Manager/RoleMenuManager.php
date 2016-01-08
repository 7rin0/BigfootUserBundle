<?php

namespace Bigfoot\Bundle\UserBundle\Manager;

use Bigfoot\Bundle\CoreBundle\Menu\Builder;
use Bigfoot\Bundle\UserBundle\Entity\RoleMenu;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class RoleMenuManager
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Builder
     */
    private $builder;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var EntityRepository
     */
    private $repository;

    /**
     * Constructor
     *
     * @param RequestStack $requestStack
     * @param Builder $builder
     * @param EntityManager $entityManager
     */
    public function __construct(RequestStack $requestStack, Builder $builder, EntityManager $entityManager)
    {
        $this->request       = $requestStack->getCurrentRequest();
        $this->builder       = $builder;
        $this->entityManager = $entityManager;
        $this->repository    = $this->entityManager->getRepository('BigfootUserBundle:RoleMenu');
    }

    /**
     * Get items
     *
     * @return array
     */
    public function getItems()
    {
        $menu  = $this->builder->createMainMenu($this->request);
        $items = $this->getChilds($menu->getChildren());

        return $items;
    }

    /**
     * Get childs
     *
     * @param  array   $items
     * @param  integer $level
     *
     * @return array
     */
    public function getChilds($items, $level = 0)
    {
        $childrens = array();

        foreach ($items as $item) {
            $roleMenu = $this->repository->findOneBySlug($item->getName());
            $child    = $roleMenu instanceof RoleMenu ? $roleMenu : new RoleMenu();
            $child
                ->setSlug($item->getName())
                ->setLabel($item->getLabel())
                ->setLevel($level);

            $childrens[]  = $child;

            if (count($item->getChildren())) {
                $elements  = $this->getChilds($item->getChildren(), $level + 1);
                $childrens = array_merge($childrens, $elements);
            }
        }

        return $childrens;
    }
}
