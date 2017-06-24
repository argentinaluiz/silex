<?php

namespace SON\Entity;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository implements UserProviderInterface
{
    private $passwordEncoder;

    public function createAdminUser($username, $password)
    {
        $user = new User();
        $user->username = $username;
        $user->plainPassword = $password;
        $user->roles = 'ROLE_ADMIN';

        $this->insert($user);

        return $user;
    }

    public function insert($user)
    {
        $this->encodePassword($user);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function setPasswordEncoder(PasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function loadUserByUsername($username)
    {
        $user = $this->findOneByUsername($username);
        if (!$user)
            throw new UsernameNotFoundException(sprintf('Usuário "%s" não existe', $username));

        return $this->arrayToObject($user->toArray());
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User)
            throw new UnsupportedUserException(sprintf('Instances od "%s" are not supported', get_class($user)));

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === 'SON\Entity\User';
    }

    public function encodePassword(User $user)
    {
        if ($user->plainPassword)
            $user->password = $this->passwordEncoder->encodePassword($user->plainPassword, $user->getSalt());
    }

    public function objectToArray(User $user)
    {
        return [
            'id'        => $user->id,
            'username'  => $user->username,
            'password'  => $user->password,
            'roles'     => implode(',', $user->roles),
            'created_at'=> $user->createdAt->format(self::DATE_FORMAT)
        ];
    }

    public function arrayToObject($userArr, $user = null)
    {
        if (!$user) {
            $user = new User();

            $user->id = isset($userArr['id']) ? $userArr['id'] : null;
        }

        $username   = isset($userArr['username']) ? $userArr['username'] : null;
        $password   = isset($userArr['password']) ? $userArr['password'] : null;
        $roles      = isset($userArr['roles']) ? explode(',', $userArr['roles']) : [];
        $createdAt  = isset($userArr['created_at']) ? \DateTime::createFromFormat(self::DATE_FORMAT, $userArr['created_at']) : null;

        if ($username) {
            $this->username = $username;
        }

        if ($password) {
            $this->password = $password;
        }

        if ($roles) {
            $this->roles = $roles;
        }

        if ($createdAt) {
            $this->createdAt = $createdAt;
        }

        return $user;
    }
}