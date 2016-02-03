<?php

namespace AppBundle\Security;

use AppBundle\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class SpookyUserProvider implements UserProviderInterface
{
    public function loadUserByUsername($username)
    {
        // "load" the user - e.g. load from the db
        $user = new User();
        $user->setUsername($username);

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        return $user;
    }

    public function supportsClass($class)
    {
        return $class == 'AppBundle\Entity\User';
    }
}