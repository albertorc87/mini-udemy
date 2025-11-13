<?php

declare(strict_types=1);

namespace Udemy\User\Role\Infrastructure\Persistence\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Udemy\User\Role\Domain\Repository\RoleRepository;
use Udemy\User\Role\Domain\Role;
use Udemy\User\Role\Domain\RoleId;
use Udemy\User\Role\Domain\RoleName;

final class DoctrineRoleRepository implements RoleRepository
{
	public function __construct(
		private readonly EntityManagerInterface $entityManager
	) {
	}

	public function save(Role $role): void
	{
		$this->entityManager->persist($role);
		$this->entityManager->flush();
	}

	public function findById(RoleId $id): ?Role
	{
		return $this->entityManager
			->getRepository(Role::class)
			->find($id->value());
	}

	public function findByName(RoleName $name): ?Role
	{
		return $this->entityManager
			->getRepository(Role::class)
			->findOneBy(['name.value' => $name->value()]);
	}

	/**
	 * @return Role[]
	 */
	public function findAll(): array
	{
		return $this->entityManager
			->getRepository(Role::class)
			->findAll();
	}
}

