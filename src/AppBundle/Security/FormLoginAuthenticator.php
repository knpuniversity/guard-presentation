<?php

namespace AppBundle\Security;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;

class FormLoginAuthenticator extends AbstractFormLoginAuthenticator
{
    private $router;

    private $em;

    public function __construct(RouterInterface $router, EntityManager $em)
    {
        $this->router = $router;
        $this->em = $em;
    }

    public function getCredentials(Request $request)
    {
        if ($request->getPathInfo() != '/login_check') {
            return;
        }

        return [
            'username' => $request->request->get('_username'),
            'password' => $request->request->get('_password'),
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $username = $credentials['username'];

        return $this->em
            ->getRepository('AppBundle:User')
            ->findOneBy(['username' => $username]);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        $password = $credentials['password'];
        if ($password != 'TheMagicWord') {
            throw new BadCredentialsException();
        }
    }

    protected function getLoginUrl()
    {
        return $this->router->generate('security_login');
    }

    protected function getDefaultSuccessRedirectUrl()
    {
        return $this->router->generate('homepage');
    }


}
