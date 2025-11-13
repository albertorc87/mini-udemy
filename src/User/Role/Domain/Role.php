<?php

declare(strict_types=1);

namespace Udemy\User\Role\Domain;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Udemy\User\Role\Domain\RoleId;
use Udemy\User\Role\Domain\RoleName;
use Udemy\User\User\Domain\User;

/**
 * Entidad Role
 * El mapeo XML está en config/doctrine/Role.Role.orm.xml
 */
class Role
{
    private RoleId $id;
    private RoleName $name;
    private \DateTimeImmutable $createdAt;
    private \DateTimeImmutable $updatedAt;

    /** @var Collection<int, User> */
    private Collection $users;

    public function __construct(
        RoleId $id,
        RoleName $name
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->users = new ArrayCollection();
    }

    public function getId(): RoleId
    {
        return $this->id;
    }

    public function getName(): RoleName
    {
        return $this->name;
    }

    public function setName(RoleName $name): void
    {
        $this->name = $name;
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
     * @return User[]
     * Retorna las entidades de usuario como un array nativo (para el dominio)
     */
    public function getUsers(): array
    {
        return $this->users->toArray();
    }
}

