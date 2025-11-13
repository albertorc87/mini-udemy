<?php

declare(strict_types=1);

namespace Udemy\User\Role\Domain\Repository;

use Udemy\User\Role\Domain\Role;
use Udemy\User\Role\Domain\RoleId;
use Udemy\User\Role\Domain\RoleName;

interface RoleRepository
{
	public function save(Role $role): void;

	public function findById(RoleId $id): ?Role;

	public function findByName(RoleName $name): ?Role;

	/**
	 * @return Role[]
	 */
	public function findAll(): array;
}

