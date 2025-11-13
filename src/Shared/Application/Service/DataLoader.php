<?php

declare(strict_types=1);

namespace Udemy\Shared\Application\Service;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Udemy\User\Role\Domain\Repository\RoleRepository;
use Udemy\User\Role\Domain\Role;
use Udemy\User\Role\Domain\RoleId;
use Udemy\User\Role\Domain\RoleName;
use Udemy\User\User\Domain\Repository\UserRepository;
use Udemy\User\User\Domain\User;
use Udemy\User\User\Domain\UserEmail;
use Udemy\User\User\Domain\UserId;
use Udemy\User\User\Domain\UserName;
use Udemy\User\User\Domain\UserPasswordHash;
use Udemy\User\User\Domain\UserStatus;

final class DataLoader
{
	private const ROLES = [
		'ROLE_STUDENT' => '01K9YTMX15EY984S5BCQM91DJA',
		'ROLE_TEACHER' => '01K9YTMX15EY984S5BCQM91DJB',
		'ROLE_ADMIN' => '01K9YTMX15EY984S5BCQM91DJC',
	];

	public function __construct(
		private readonly RoleRepository $roleRepository,
		private readonly UserRepository $userRepository,
		private readonly UserPasswordHasherInterface $passwordHasher
	) {
	}

	public function loadRoles(): void
	{
		foreach (self::ROLES as $roleName => $roleId) {
			// Verificar si el rol ya existe
			$roleNameValueObject = new RoleName($roleName);
			$existingRole = $this->roleRepository->findByName($roleNameValueObject);
			
			if ($existingRole !== null) {
				continue; // Rol ya existe, no crear duplicado
			}

			// Crear rol directamente con el ID específico
			$role = new Role(
				new RoleId($roleId),
				$roleNameValueObject
			);

			$this->roleRepository->save($role);
		}
	}

	public function loadTestUser(): void
	{
		// Verificar si el usuario de prueba ya existe
		$testEmail = new UserEmail('alberto.r.caballero.87@gmail.com');
		$existingUser = $this->userRepository->findByEmail($testEmail);
		
		if ($existingUser !== null) {
			return; // Usuario ya existe, no crear duplicado
		}

		// Crear Value Objects
		$userId = new UserId('01K9YTMX15EY984S5BCQM91DJH');
		$userEmail = new UserEmail('alberto.r.caballero.87@gmail.com');
		$userName = new UserName('Alberto');
		$userAvatarUrl = null;

		// Crear un usuario temporal para hashear la contraseña
		// Symfony PasswordHasher necesita un objeto que implemente PasswordAuthenticatedUserInterface
		$temporaryUser = new User(
			$userId,
			$userEmail,
			new UserPasswordHash(''), // Hash temporal, solo para hashear
			$userName,
			$userAvatarUrl
		);

		// Hashear la contraseña (infraestructura)
		$hashedPassword = $this->passwordHasher->hashPassword($temporaryUser, 'admin123');
		$userPasswordHash = new UserPasswordHash($hashedPassword);

		// Crear el usuario directamente usando el constructor (sin eventos)
		// No usamos User::create() para evitar disparar eventos de dominio
		$user = new User(
			$userId,
			$userEmail,
			$userPasswordHash,
			$userName,
			$userAvatarUrl,
			UserStatus::active() // Usuario activo (confirmado)
		);

		// Asignar todos los roles al usuario de prueba
		foreach (self::ROLES as $roleName => $roleId) {
			$role = $this->roleRepository->findByName(new RoleName($roleName));
			if ($role !== null) {
				$user->addRole($role);
			}
		}

		// Guardar el usuario con los roles asignados
		$this->userRepository->save($user);
	}

	public function loadAll(): void
	{
		$this->loadRoles();
		$this->loadTestUser();
	}
}

