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
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use DateTimeInterface;

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

    /** @var Collection<int, Role> */ // Usar la anotación para mayor claridad (opcional)
    // ANTES: private array $roles = [];

    // DESPUÉS: Cambiar el tipo y la inicialización
    private Collection $roles;

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
        $this->roles = new ArrayCollection();
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
			$name->value(),
            uniqid(),
            (new \DateTimeImmutable())->format(DateTimeInterface::ATOM),
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

    public function addRole(Role $role): void
    {
        // Usar el método 'contains' de la Collection
        if (!$this->roles->contains($role)) { 
            // Usar el método 'add' de la Collection
            $this->roles->add($role); 
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function removeRole(Role $role): void
    {
        // Usar el método 'removeElement' de la Collection
        if ($this->roles->contains($role)) {
            $this->roles->removeElement($role);
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    /**
     * @return Role[]
     * Retorna las entidades de rol como un array nativo (para el dominio)
     */
    public function getRoleEntities(): array
    {
        // Convertir la Collection a un array nativo si es necesario fuera del mapeo
        return $this->roles->toArray(); 
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
        // La Collection es iterable, por lo que array_map funciona, 
        // pero es más explícito usar toArray() primero si quieres un array nativo.
        return array_map(
            fn(Role $role) => $role->getName()->value(),
            $this->roles->toArray() // Mejor ser explícito para evitar problemas de iteración
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
