<?php

declare(strict_types=1);

namespace Udemy\User\User\Domain;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Udemy\Shared\Domain\Aggregate\AggregateRoot;
use Udemy\User\Role\Domain\Role;
use Udemy\User\User\Domain\Event\UserCreated;
use Udemy\User\User\Domain\UserAvatarUrl;
use Udemy\User\User\Domain\UserEmail;
use Udemy\User\User\Domain\UserName;
use Udemy\User\User\Domain\UserPasswordHash;
use Udemy\User\User\Domain\Service\UserEmailUniquenessChecker;
use Udemy\User\User\Domain\UserStatus;
use Udemy\User\User\Domain\UserId;

/**
 * Entidad User que implementa UserInterface para Symfony Security
 * El mapeo XML está en config/doctrine/User.User.orm.xml
 */
class User extends AggregateRoot implements UserInterface, PasswordAuthenticatedUserInterface
{
    private UserId $id;
    private UserEmail $email;
    private UserPasswordHash $password;
    private UserName $name;
    private ?UserAvatarUrl $avatarUrl;
    private UserStatus $status;
    private \DateTimeImmutable $createdAt;
    private \DateTimeImmutable $updatedAt;

    /** @var Role[] */
    private array $roles = [];

    public function __construct(
        UserId $id,
        UserEmail $email,
        UserPasswordHash $password,
        UserName $name,
        ?UserAvatarUrl $avatarUrl = null,
        ?UserStatus $status = null
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->name = $name;
        $this->avatarUrl = $avatarUrl;
        $this->status = $status ?? UserStatus::pending();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

	/**
	 * Factory method para crear un nuevo usuario
	 * Encapsula la lógica de creación y validación de reglas de negocio
	 */
	public static function create(
		UserId $id,
		UserEmail $email,
		UserPasswordHash $password,
		UserName $name,
		?UserAvatarUrl $avatarUrl,
		UserEmailUniquenessChecker $emailUniquenessChecker
	): self {
		// Validar regla de negocio: email debe ser único
		$emailUniquenessChecker->ensureEmailIsUnique($email);

		// Crear instancia
		$user = new self($id, $email, $password, $name, $avatarUrl);

		// Registrar evento de dominio
		$user->record(new UserCreated(
			$id->value(),
			$email->value(),
			$name->value()
		));

		return $user;
	}

    public function getId(): UserId
    {
        return $this->id;
    }

    public function getEmail(): UserEmail
    {
        return $this->email;
    }

    public function setEmail(UserEmail $email): void
    {
        $this->email = $email;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getName(): UserName
    {
        return $this->name;
    }

    public function setName(UserName $name): void
    {
        $this->name = $name;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getAvatarUrl(): ?UserAvatarUrl
    {
        return $this->avatarUrl;
    }

    public function setAvatarUrl(?UserAvatarUrl $avatarUrl): void
    {
        $this->avatarUrl = $avatarUrl;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getStatus(): UserStatus
    {
        return $this->status;
    }

    public function setStatus(UserStatus $status): void
    {
        $this->status = $status;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Añade un rol al usuario
     */
    public function addRole(Role $role): void
    {
        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    /**
     * Elimina un rol del usuario
     */
    public function removeRole(Role $role): void
    {
        $key = array_search($role, $this->roles, true);
        if ($key !== false) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    /**
     * @return Role[]
     */
    public function getRoleEntities(): array
    {
        return $this->roles;
    }

    // ========== UserInterface Implementation ==========

    /**
     * Retorna el identificador único del usuario (email)
     * Symfony Security espera un string
     */
    public function getUserIdentifier(): string
    {
        return $this->email->value();
    }

    /**
     * Retorna los roles como array de strings para Symfony Security
     * Symfony Security espera un array de strings como ['ROLE_USER', 'ROLE_ADMIN']
     */
    public function getRoles(): array
    {
        return array_map(
            fn(Role $role) => $role->getName()->value(),
            $this->roles
        );
    }

    /**
     * Retorna la contraseña hasheada
     * Symfony Security espera un string
     */
    public function getPassword(): string
    {
        return $this->password->value();
    }

    /**
     * Actualiza la contraseña
     */
    public function setPassword(UserPasswordHash $password): void
    {
        $this->password = $password;
        $this->updatedAt = new \DateTimeImmutable();
    }

    /**
     * Symfony Security: eliminar información sensible del usuario
     */
    public function eraseCredentials(): void
    {
        // Si tienes información sensible temporal, elimínala aquí
    }

    /**
     * Symfony Security: retorna el identificador único del usuario
     * (alias de getUserIdentifier para compatibilidad)
     */
    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }
}
