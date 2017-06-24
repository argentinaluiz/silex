<?php

namespace SON\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="SON\Entity\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    public $id;
    /**
     * @ORM\Column(type="string", length=100)
     */
    public $username;
    /**
     * @ORM\Column(type="string", length=100)
     */
    public $password;
    /**
     * @ORM\Column(type="string", length=100)
     */
    public $plainPassword;
    /**
     * @ORM\Column(type="string", length=100)
     */
    public $roles = ['ROLE_USER'];
    /**
     * @ORM\Column(type="datetime")
     */
    public $createdAt;

    function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param mixed $plainPassword
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * @return mixed
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param mixed $roles
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    public function __toString()
    {
        return $this->getUsername();
    }

    public function toArray()
    {
        return [
            'id'        => $this->id,
            'username'  => $this->getUsername(),
            'salt'      => $this->getSalt(),
            'roles'     => $this->getRoles(),
            'password'  => $this->getPassword()];
    }
}