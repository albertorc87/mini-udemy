<?php

declare(strict_types=1);

namespace Udemy\User\User\Application\Service;

use DomainException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Udemy\User\User\Domain\Repository\UserRepository;
use Udemy\User\User\Domain\UserId;
use Udemy\User\User\Domain\UserStatus;

final class UserConfirmer
{
	public function __construct(
		private readonly UserRepository $userRepository
	) {
	}

	public function __invoke(string $jwtToken): void
	{
		$payload = JWT::decode($jwtToken, new Key($_ENV['EMAIL_SECRET'], 'HS256'));
		$userId = $payload->userId;

		$user = $this->userRepository->findById(new UserId($userId));

		if ($user === null) {
			throw new DomainException('User not found');
		}

		if ($user->getStatus()->isActive()) {
			throw new DomainException('User is already confirmed');
		}

		if ($user->getStatus()->isBanned()) {
			throw new DomainException('Cannot confirm a banned user');
		}

		$user->setStatus(UserStatus::active());
		$this->userRepository->save($user);
	}
}

