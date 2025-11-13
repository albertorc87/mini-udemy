<?php

declare(strict_types=1);

namespace Udemy\User\User\Infrastructure\Persistence\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Udemy\User\User\Domain\Repository\UserRepository;
use Udemy\User\User\Domain\User;
use Udemy\User\User\Domain\UserEmail;
use Udemy\User\User\Domain\UserId;

final class DoctrineUserRepository implements UserRepository
{
	public function __construct(
		private readonly EntityManagerInterface $entityManager
	) {
	}

	public function save(User $user): void
	{
		$this->entityManager->persist($user);
		$this->entityManager->flush();
	}

	public function findByEmail(UserEmail $email): ?User
	{
		return $this->entityManager
			->getRepository(User::class)
			->findOneBy(['email.value' => $email->value()]);
	}

	public function findById(UserId $id): ?User
	{
		return $this->entityManager
			->getRepository(User::class)
			->find($id->value());
	}
}

