<?php

namespace AppBundle\Entity;

use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface
{
    private $username;

    public function __construct($username = null)
    {
        $this->username = $username;
    }

    public function getUsername()
    {
        return $this->username;
    }
    
    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    public function getPassword()
    {
    }
    public function getSalt()
    {
    }
    public function eraseCredentials()
    {
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }
}