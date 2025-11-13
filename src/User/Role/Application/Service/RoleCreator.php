<?php

declare(strict_types=1);

namespace Udemy\User\Role\Application\Service;

use Symfony\Component\Uid\Ulid;
use Udemy\User\Role\Domain\Repository\RoleRepository;
use Udemy\User\Role\Domain\Role;
use Udemy\User\Role\Domain\RoleId;
use Udemy\User\Role\Domain\RoleName;

final class RoleCreator
{
	public function __construct(
		private readonly RoleRepository $roleRepository
	) {
	}

	public function __invoke(string $roleName): Role
	{
		$roleNameValueObject = new RoleName($roleName);

		// TODO: Esto hay que hacerlo como en UserCreator con un metodo estatico create()
		$existingRole = $this->roleRepository->findByName($roleNameValueObject);
		if ($existingRole !== null) {
			return $existingRole;
		}

		// Crear nuevo rol
		$roleId = new RoleId(Ulid::generate());
		$role = new Role($roleId, $roleNameValueObject);

		$this->roleRepository->save($role);

		return $role;
	}
}

