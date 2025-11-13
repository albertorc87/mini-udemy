<?php

declare(strict_types=1);

namespace Udemy\User\User\Application\Command;

use Udemy\Shared\Domain\Bus\Command\Command;

final class ConfirmUserCommand implements Command
{
	public function __construct(
		public readonly string $jwtToken,
	) {
	}
}

