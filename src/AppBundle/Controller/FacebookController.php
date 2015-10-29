<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class FacebookController extends Controller
{
    /**
     * @Route("/connect/facebook", name="connect_facebook")
     */
    public function connectFacebookAction()
    {
        // redirect to Facebook
        $facebookOAuthProvider = $this->get('app.facebook_provider');

        $url = $facebookOAuthProvider->getAuthorizationUrl([
            // these are actually the default scopes
            'scopes' => ['public_profile', 'email'],
        ]);

        return $this->redirect($url);
    }

    /**
     * @Route("/connect/facebook-check", name="connect_facebook_check")
     */
    public function connectFacebookActionCheck()
    {
        // will not be reached!
    }
}