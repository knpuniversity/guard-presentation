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
        return $this->get('knpu.oauth2.client.facebook')
            ->redirect(['public_profile', 'email']);
    }

    /**
     * @Route("/connect/facebook-check", name="connect_facebook_check")
     */
    public function connectFacebookActionCheck()
    {
        // will not be reached!
    }
}