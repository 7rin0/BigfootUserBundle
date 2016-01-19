<?php

namespace Bigfoot\Bundle\UserBundle\Controller;

use Bigfoot\Bundle\CoreBundle\Controller\CrudController;
use Bigfoot\Bundle\UserBundle\Event\UserEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * User controller.
 *
 * @Cache(maxage="0", smaxage="0", public="false")
 * @Route("/user")
 */
class UserController extends CrudController
{
    /**
     * @return string
     */
    protected function getName()
    {
        return 'admin_user';
    }

    /**
     * @return string
     */
    protected function getEntity()
    {
        return 'BigfootUserBundle:User';
    }

    protected function getFields()
    {
        return array(
            'id' => array(
                'label' => 'ID',
            ),
            'username' => array(
                'label' => 'Username',
            ),
        );
    }

    public function getFilters()
    {
        return array(
            array(
                'placeholder' => 'Username',
                'name'        => 'username',
                'type'        => 'referer',
                'options' => array(
                    'property' => 'username'
                )
            )
        );
    }

    protected function getFormType()
    {
        return 'admin_user';
    }

    public function getEntityLabel()
    {
        return 'User';
    }

    /**
     * Lists User entities.
     *
     * @Route("/", name="admin_user")
     * @param RequestStack $requestStack
     * @return array
     */
    public function indexAction()
    {
        return $this->doIndex();
    }

    /**
     * New User entity.
     *
     * @Route("/new", name="admin_user_new")
     */
    public function newAction()
    {
        return $this->doNew();
    }

    /**
     * Edit User entity.
     *
     * @Route("/edit/{id}", name="admin_user_edit")
     */
    public function editAction($id)
    {
        return $this->doEdit($id);
    }

    /**
     * Delete User entity.
     *
     * @Route("/delete/{id}", name="admin_user_delete")
     */
    public function deleteAction($id)
    {
        return $this->doDelete($id);
    }

    /**
     * PrePersist User entity.
     */
    protected function prePersist($user, $action)
    {
        $this->getEventDispatcher()->dispatch(UserEvent::UPDATE_PROFILE, new GenericEvent($user));
    }

    /**
     * Post flush entity
     *
     * @param object $user user
     */
    protected function postFlush($user, $action)
    {
        $this->getEventDispatcher()->dispatch(UserEvent::REFRESH_USER, new GenericEvent($user));
    }
}
