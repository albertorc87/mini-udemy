<?php

declare(strict_types=1);

namespace Udemy\User\User\Domain\Service;

use Udemy\User\User\Domain\Repository\UserRepository;
use Udemy\User\User\Domain\UserEmail;

final class UserEmailUniquenessChecker
{
	public function __construct(
		private readonly UserRepository $userRepository
	) {
	}

	public function ensureEmailIsUnique(UserEmail $email): void
	{
		$existingUser = $this->userRepository->findByEmail($email);

		if ($existingUser !== null) {
			throw new \DomainException(
				sprintf('User with email "%s" already exists', $email->value())
			);
		}
	}
}

