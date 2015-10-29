<?php

namespace AppBundle\Security;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class ApiTokenAuthenticator extends AbstractGuardAuthenticator
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getCredentials(Request $request)
    {
        return $request->headers->get('X-API-TOKEN');
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $apiToken = $credentials;

        $user = $this->em
            ->getRepository('AppBundle:User')
            ->findOneBy(['apiToken' => $apiToken]);

        if (!$user) {
            throw new CustomUserMessageAuthenticationException(
                'This is a really un-cool api token.'
            );
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        // no credentials to check
        return;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse([
            'message' => 'That API token was not normcore'
        ], 401);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // let the request continue to the controller
        return;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        throw new \Exception('This is not used in our app');
    }

    public function supportsRememberMe()
    {
    }
}
