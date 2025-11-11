<?php

declare(strict_types=1);

namespace Udemy\Shared\Infrastructure\Persistence\Doctrine\Type;

use Udemy\Course\Course\Domain\CourseId;

/**
 * Custom Type para mapear CourseId Value Object
 */
final class CourseIdType extends UlidValueObjectType
{
	public function getName(): string
	{
		return 'course_id';
	}

	protected function getValueObjectClass(): string
	{
		return CourseId::class;
	}
}

