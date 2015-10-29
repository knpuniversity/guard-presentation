<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class RegistrationController extends Controller
{
    /**
     * @Route("/register")
     */
    public function registerAction(Request $request)
    {
        $user = new User();
        $form = $this->createFormBuilder($user)
            ->add('username')
            ->add('email')
            ->getForm();

        $form->handleRequest($request);
        if ($form->isValid()) {
            $user->setApiToken('0000');

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->container
                ->get('security.authentication.guard_handler')
                ->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $this->get('form_login_authenticator'),
                    // the name of your firewall
                    'main'
                );

            return $this->redirectToRoute('secure_page');
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
}