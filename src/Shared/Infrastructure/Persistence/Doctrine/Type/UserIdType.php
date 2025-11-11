<?php

declare(strict_types=1);

namespace Udemy\Shared\Infrastructure\Persistence\Doctrine\Type;

use Udemy\User\User\Domain\UserId;

/**
 * Custom Type para mapear UserId Value Object
 */
final class UserIdType extends UlidValueObjectType
{
	public function getName(): string
	{
		return 'user_id';
	}

	protected function getValueObjectClass(): string
	{
		return UserId::class;
	}
}

