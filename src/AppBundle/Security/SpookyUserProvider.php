<?php

namespace AppBundle\Security;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class SpookyUserProvider implements UserProviderInterface
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function loadUserByUsername($username)
    {
        $user = $this->em->getRepository('AppBundle:User')
            ->findOneBy(['username' => $username]);

        if (!$user) {
            throw new UsernameNotFoundException();
        }

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        return $this->em->getRepository('AppBundle:User')
            ->findOneBy(['username' => $user->getUsername()]);
    }

    public function supportsClass($class)
    {
        return $class == 'AppBundle\Entity\User';
    }
}
