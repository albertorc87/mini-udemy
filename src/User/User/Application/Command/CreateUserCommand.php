<?php

declare(strict_types=1);

namespace Udemy\User\User\Application\Command;

use Udemy\Shared\Domain\Bus\Command\Command;

final class CreateUserCommand implements Command
{
	public function __construct(
		public readonly string $email,
		public readonly string $password,
		public readonly string $name,
		public readonly ?string $avatarUrl = null
	) {
	}
}

