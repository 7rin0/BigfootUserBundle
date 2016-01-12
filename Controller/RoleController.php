<?php

namespace Bigfoot\Bundle\UserBundle\Controller;

use Bigfoot\Bundle\CoreBundle\Controller\CrudController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Role controller.
 *
 * @Cache(maxage="0", smaxage="0", public="false")
 * @Route("/role")
 */
class RoleController extends CrudController
{
    /**
     * @return string
     */
    protected function getName()
    {
        return 'admin_role';
    }

    /**
     * @return string
     */
    protected function getEntity()
    {
        return 'BigfootUserBundle:Role';
    }

    public function getEntityLabel()
    {
        return 'User role';
    }

    protected function getFields()
    {
        return array(
            'id'    => array(
                'label' => 'ID',
            ),
            'label' => array(
                'label' => 'Label',
            ),
        );
    }

    protected function getFilters()
    {
        return array(
            array(
                'placeholder' => 'Label',
                'name'        => 'label',
                'type'        => 'referer',
                'options' => array(
                    'property' => 'label'
                )
            )
        );
    }

    protected function getFormType()
    {
        return 'admin_role';
    }

    /**
     * List Role entities.
     *
     * @Route("/", name="admin_role")
     * @param RequestStack $requestStack
     * @return array
     */
    public function indexAction(RequestStack $requestStack)
    {
        return $this->doIndex($requestStack->getCurrentRequest());
    }
    /**
     * New Role entity.
     *
     * @Route("/new", name="admin_role_new")
     */
    public function newAction(RequestStack $requestStack)
    {
        return $this->doNew($requestStack->getCurrentRequest());
    }

    /**
     * Edit Role entity.
     *
     * @Route("/edit/{id}", name="admin_role_edit")
     */
    public function editAction(RequestStack $requestStack, $id)
    {
        return $this->doEdit($requestStack->getCurrentRequest(), $id);
    }

    /**
     * Delete Role entity.
     *
     * @Route("/delete/{id}", name="admin_role_delete")
     */
    public function deleteAction(RequestStack $requestStack, $id)
    {
        return $this->doDelete($requestStack->getCurrentRequest(), $id);
    }
}
