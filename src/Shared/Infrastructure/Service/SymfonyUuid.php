<?php

declare(strict_types=1);

namespace Udemy\Shared\Infrastructure\Service;

use Udemy\Shared\Domain\Uuid as UuidInterface;
use Symfony\Component\Uid\Ulid;

final class SymfonyUuid implements UuidInterface
{
	public function next(): string
	{
		return Ulid::generate();
	}
}