<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Reurbano\UserBundle\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class DocumentUserProvider implements UserProviderInterface
{
    protected $class;
    protected $repository;
    protected $em;

    public function __construct($em, $class)
    {
        $this->class = $class;

        if (false !== strpos($this->class, ':')) {
            $this->class = $em->getClassMetadata($class)->getName();
        }

        $this->repository = $em->getRepository($class);
        $this->em = $em;
    }
    
    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        if (!$this->repository instanceof UserProviderInterface) {
            throw new \InvalidArgumentException(sprintf('O repositório "%s" precisa implementar UserProviderInterface.', get_class($this->repository)));
        }
        $user = $this->repository->loadUserByUsername($username);

        if (null === $user) {
            throw new UsernameNotFoundException(sprintf('Usuário "%s" não encontrado.', $username));
        }

        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof $this->class) {
            throw new UnsupportedUserException(sprintf('Instâncias de "%s" não são suportadas.', get_class($user)));
        }
        return $this->loadUserByUsername($user->getEmail());
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        return $class === $this->class;
    }
    public function updateUser(UserInterface $user){
        $this->em->persist($user);
        $this->em->flush();
    }
}