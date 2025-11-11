<?php

declare(strict_types=1);

namespace Udemy\User\User\Infrastructure\Security;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Udemy\User\User\Domain\User;
use Udemy\User\User\Domain\UserEmail;

/**
 * UserProvider personalizado que carga usuarios desde la base de datos
 * y sus roles desde la tabla user_role
 */
final class UserProvider implements UserProviderInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Carga un usuario por su identificador (email)
     */
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        // Crear UserEmail Value Object para la búsqueda
        $email = new UserEmail($identifier);
        
        $user = $this->entityManager
            ->getRepository(User::class)
            ->createQueryBuilder('u')
            ->where('u.email.value = :email')
            ->setParameter('email', $email->value())
            ->getQuery()
            ->getOneOrNullResult();

        if (!$user) {
            throw new UserNotFoundException(sprintf('User with email "%s" not found.', $identifier));
        }

        // Doctrine carga automáticamente los roles gracias al mapeo XML many-to-many
        return $user;
    }

    /**
     * Recarga un usuario desde la base de datos
     * Útil después de actualizar el usuario en la sesión
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }

        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    /**
     * Verifica si este provider soporta la clase de usuario
     */
    public function supportsClass(string $class): bool
    {
        return User::class === $class || is_subclass_of($class, User::class);
    }
}

