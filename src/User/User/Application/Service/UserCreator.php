<?php

declare(strict_types=1);

namespace Udemy\User\User\Application\Service;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Ulid;
use Udemy\Shared\Domain\Bus\Event\EventBus;
use Udemy\User\User\Domain\Repository\UserRepository;
use Udemy\User\User\Domain\Service\UserEmailUniquenessChecker;
use Udemy\User\User\Domain\User;
use Udemy\User\User\Domain\UserEmail;
use Udemy\User\User\Domain\UserId;
use Udemy\User\User\Domain\UserName;
use Udemy\User\User\Domain\UserPasswordHash;
use Udemy\User\User\Domain\UserAvatarUrl;

final class UserCreator
{
	public function __construct(
		private readonly UserRepository $userRepository,
		private readonly UserPasswordHasherInterface $passwordHasher,
		private readonly UserEmailUniquenessChecker $emailUniquenessChecker,
		private readonly EventBus $eventBus
	) {
	}

	public function __invoke(
		string $email,
		string $password,
		string $name,
		?string $avatarUrl = null
	): void {
		// Crear Value Objects
		$userId = new UserId(Ulid::generate());
		$userEmail = new UserEmail($email);
		$userName = new UserName($name);
		$userAvatarUrl = $avatarUrl ? new UserAvatarUrl($avatarUrl) : null;

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
		$hashedPassword = $this->passwordHasher->hashPassword($temporaryUser, $password);
		$userPasswordHash = new UserPasswordHash($hashedPassword);

		// Crear el usuario usando el factory method del dominio
		// El método create() valida las reglas de negocio y retorna la instancia
		$user = User::create(
			$userId,
			$userEmail,
			$userPasswordHash,
			$userName,
			$userAvatarUrl,
			$this->emailUniquenessChecker
		);

		// Persistir el usuario
		$this->userRepository->save($user);

		// Publicar eventos de dominio
		$domainEvents = $user->pullDomainEvents();
		$this->eventBus->publish(...$domainEvents);
	}
}

