<?php

namespace Bigfoot\Bundle\UserBundle\Controller;

use Bigfoot\Bundle\CoreBundle\Controller\BaseController;
use Bigfoot\Bundle\UserBundle\Form\RoleMenuType;
use Bigfoot\Bundle\UserBundle\Form\Type\RoleMenusType;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * RoleMenu controller.
 *
 * @Cache(maxage="0", smaxage="0", public="false")
 * @Route("/role/menu")
 */
class RoleMenuController extends BaseController
{
    private function getMenuRoleManager()
    {
        return $this->container->get('bigfoot_user.manager.role_menu');
    }

    /**
     * Lists all Role entities.
     *
     * @Route("/", name="admin_role_menu")
     * @Method({"GET", "POST"})
     */
    public function indexAction(RequestStack $requestStack)
    {
        $items = $this->getMenuRoleManager()->getItems();
        $requestStack = $requestStack->getCurrentRequest();
        $form  = $this->createForm(new RoleMenusType(), array('roleMenus' => new ArrayCollection($items)));

        if ($requestStack->isMethod('POST')) {
            $form->handleRequest($requestStack);

            $datas     = $form->getData();
            $roleMenus = $datas['roleMenus'];

            foreach ($roleMenus as $roleMenu) {
                $this->persistAndFlush($roleMenu);
            }
        }

        return $this->render(
            $this->getThemeBundle().':RoleMenu:index.html.twig',
            array(
            'items' => $items,
            'form'  => $form->createview()
        ));
    }
}
