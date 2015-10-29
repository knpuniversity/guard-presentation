<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function homepageAction()
    {
        return $this->render('default/homepage.html.twig');
    }

    /**
     * @Route("/secure", name="secure_page")
     * @Security("is_granted('ROLE_USER')")
     */
    public function secureAction()
    {
        return new JsonResponse([
            'message' => 'Hello from the secureAction!'
        ]);

        return $this->render('default/secure.html.twig');
    }
}
