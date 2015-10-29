<?php

namespace AppBundle\Security;

use AppBundle\Entity\User;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\FacebookUser;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class FacebookAuthenticator extends AbstractGuardAuthenticator
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getCredentials(Request $request)
    {
        if ($request->getPathInfo() != '/connect/facebook-check') {
            return;
        }

        if ($code = $request->query->get('code')) {
            return $code;
        }

        throw new CustomUserMessageAuthenticationException(
            'Dude, you messed up authorizing us in Facebook!'
        );
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $authorizationCode = $credentials;

        $facebookProvider = $this->container->get('app.facebook_provider');

        try {
            // the credentials are really the access token
            $accessToken = $facebookProvider->getAccessToken(
                'authorization_code',
                ['code' => $authorizationCode]
            );
        } catch (IdentityProviderException $e) {
            // you could parse the response to see the problem
            return;
        }

        /** @var FacebookUser $facebookUser */
        $facebookUser = $facebookProvider->getResourceOwner($accessToken);

        $em = $this->container->get('doctrine')->getManager();

        // 1) have they logged in with Facebook before? Easy!
        $user = $em->getRepository('AppBundle:User')
            ->findOneBy(array('email' => $facebookUser->getEmail()));
        if ($user) {
            return $user;
        }

        // 2) no user? Perhaps you just want to create one
        //  (or redirect to a registration)
        $user = new User(
            $facebookUser->getName(),
            sha1('SOME_RANDOM_STRING'.mt_rand()),
            $facebookUser->getEmail()
        );
        $em->persist($user);
        $em->flush();

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        // TODO: Implement checkCredentials() method.
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        // this would happen if something went wrong in the OAuth flow
        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);

        $url = $this->container->get('router')
            ->generate('security_login');

        return new RedirectResponse($url);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $targetPath = $request->getSession()->get('_security.'.$providerKey.'.target_path');

        if (!$targetPath) {
            $router = $this->container->get('router');
            $targetPath = $router->generate('homepage');
        }

        return new RedirectResponse($targetPath);
    }

    public function supportsRememberMe()
    {
        return true;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        throw new \Exception('not used in our app');
    }
}