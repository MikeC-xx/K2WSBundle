<?php

namespace K2WSBundle\Security\User;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

class WebserviceUser implements UserInterface, EquatableInterface
{

    /** ContactPersonId */
    private $id;

    /** LoginNameCalc */
    private $username;

    /** CFPass */
    private $password;

    /** Password salt, currently not used */
    private $salt;

    /** Name */
    private $firstName;

    /** Surname */
    private $lastName;

    /** TimeStamp */
    private $timestamp;

    /** User roles */
    private $roles;

    public function __construct($id, $username, $password, $firstName, $lastName, $timeStamp, array $roles)
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->salt = '';
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->timeStamp = $timeStamp;
        $this->roles = $roles;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getTimeStamp()
    {
        return $this->timeStamp;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getId()
    {
        return $this->id;
    }

    public function eraseCredentials()
    {
    }

    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof WebserviceUser) {
            return false;
        }

        if ($this->getId() !== $user->getId()) {
            return false;
        }

        if ($this->getPassword() !== $user->getPassword()) {
            return false;
        }

        if ($this->getUsername() !== $user->getUsername()) {
            return false;
        }

        return true;
    }
}