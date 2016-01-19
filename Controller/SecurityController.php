<?php

namespace Bigfoot\Bundle\UserBundle\Controller;

use Bigfoot\Bundle\CoreBundle\Controller\BaseController;
use Bigfoot\Bundle\UserBundle\Event\UserEvent;
use Bigfoot\Bundle\UserBundle\Form\Model\ForgotPasswordModel;
use Bigfoot\Bundle\UserBundle\Form\Model\ResetPasswordModel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * BigfootUser controller.
 *
 * @Cache(maxage="0", smaxage="0", public="false")
 * @Route("/")
 */
class SecurityController extends BaseController
{
    /**
     * @Route("/login", name="admin_login")
     */
    public function loginAction(RequestStack $requestStack = null)
    {
        $helper = $this->get('security.authentication_utils');

        return $this->render(
            $this->getThemeBundle().':user:login.html.twig',
            array(
                'last_username' => $helper->getLastUsername(),
                'error'         => $helper->getLastAuthenticationError(),
            )
        );
    }

    /**
     * @Route("/login_check", name="admin_login_check")
     */
    public function loginCheckAction()
    {
        throw new \RuntimeException('You must configure the check path to be handled by the firewall using form_login in your security firewall configuration.');
    }

    /**
     * @Route("/logout", name="admin_logout")
     */
    public function logoutAction()
    {
        throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
    }

    /**
     * Forgot password
     *
     * @Route("/forgot-password", name="admin_forgot_password")
     */
    public function forgotPasswordAction(RequestStack $requestStack = null)
    {
        // Create Form
        $form         = $this->createForm(
            get_class($this->get('bigfoot_user.form.type.forgot_password')),
            new ForgotPasswordModel()
        );

        // If Request to Current user is set
        if($requestStack) {
            $requestStack = $this->getRequestStack();

            if ('POST' === $requestStack->getMethod()) {
                $form->handleRequest($requestStack);

                if ($form->isValid()) {
                    $user = $form->get('email')->getData();

                    if ($user->isPasswordRequestNonExpired($this->getParameter('bigfoot_user.resetting.token_ttl'))) {
                        return $this->renderAjax(false, $this->getTranslator()->trans('Request already sent, check your emails'));
                    }

                    $token = $this->getUserManager()->generateToken($user);

                    if ($requestStack->isXmlHttpRequest()) {
                        return $this->renderAjax($token['status'], $this->getTranslator()->trans($token['message']));
                    } else {
                        return $this->redirect($this->generateUrl('admin_forgot_password'));
                    }
                } else {
                    if ($requestStack->isXmlHttpRequest()) {
                        return $this->renderAjax(false, $this->getTranslator()->trans('Invalid email'));
                    }
                }
            }
        }

        return $this->render(
            $this->getThemeBundle().':user:forgot_password.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Reset password
     *
     * @Route("/reset-password/{confirmationToken}", name="admin_reset_password")
     */
    public function resetPasswordAction($confirmationToken)
    {
        $user     = $this->getRepository('BigfootUserBundle:User')->findOneByConfirmationToken($confirmationToken);
        $tokenTtl = $this->getParameter('bigfoot_user.resetting.token_ttl');
        $requestStack = $this->getRequestStack();

        if (!$user || !$user->isPasswordRequestNonExpired($tokenTtl)) {
            return $this->redirect($this->generateUrl('admin_login'));
        }

        $form = $this->createForm(
            $this->get('bigfoot_user.form.type.reset_password'),
            new ResetPasswordModel()
        );

        if ('POST' === $requestStack->getMethod()) {
            $form->handleRequest($requestStack);

            if ($form->isValid()) {
                $data = $form->getData();

                $user->setPlainPassword($data->plainPassword);

                $this->getEventDispatcher()->dispatch(UserEvent::RESET_PASSWORD, new GenericEvent($user));

                $this->addFlash('success', 'Your password has been reset successfully');

                return $this->redirect($this->generateUrl('admin_home'));
            }
        }

        return $this->render(
            $this->getThemeBundle().':user:reset_password.html.twig',
            array(
                'form'              => $form->createView(),
                'confirmationToken' => $confirmationToken,
            )
        );
    }
}
