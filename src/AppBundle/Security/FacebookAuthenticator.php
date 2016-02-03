<?php

namespace AppBundle\Security;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use KnpU\OAuth2ClientBundle\Client\OAuth2Client;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\FacebookUser;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class FacebookAuthenticator extends AbstractGuardAuthenticator
{
    private $oAuth2Client;
    /**
     * @var EntityManager
     */
    private $em;
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(OAuth2Client $oAuth2Client, EntityManager $em, RouterInterface $router)
    {
        $this->oAuth2Client = $oAuth2Client;
        $this->em = $em;
        $this->router = $router;
    }

    public function getCredentials(Request $request)
    {
        if ($request->getPathInfo() != '/connect/facebook-check') {
            return;
        }

        try {
            return $this->oAuth2Client->getAccessToken($request);
        } catch (IdentityProviderException $e) {
            // you could parse the response to see the problem

            throw $e;
        }
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /** @var AccessToken $accessToken */
        $accessToken = $credentials;

        /** @var FacebookUser $facebookUser */
        $facebookUser = $this->oAuth2Client
            ->fetchUserFromToken($accessToken);

        // 1) have they logged in with Facebook before? Easy!
        $user = $this->em->getRepository('AppBundle:User')
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
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        // this would happen if something went wrong in the OAuth flow
        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);

        $url = $this->router
            ->generate('security_login');

        return new RedirectResponse($url);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $targetPath = $request->getSession()->get('_security.'.$providerKey.'.target_path');

        if (!$targetPath) {
            $targetPath = $this->router->generate('homepage');
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