<?php

declare(strict_types=1);

namespace Udemy\Shared\Infrastructure\Persistence\Doctrine\Type;

use Udemy\User\Role\Domain\RoleId;

/**
 * Custom Type para mapear RoleId Value Object
 */
final class RoleIdType extends UlidValueObjectType
{
	public function getName(): string
	{
		return 'role_id';
	}

	protected function getValueObjectClass(): string
	{
		return RoleId::class;
	}
}

