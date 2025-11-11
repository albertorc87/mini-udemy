<?php

declare(strict_types=1);

namespace Udemy\Shared\Infrastructure\Persistence\Doctrine\Type;

use Udemy\User\Course\Domain\UserCourseId;

/**
 * Custom Type para mapear UserCourseId Value Object
 */
final class UserCourseIdType extends UlidValueObjectType
{
	public function getName(): string
	{
		return 'user_course_id';
	}

	protected function getValueObjectClass(): string
	{
		return UserCourseId::class;
	}
}

