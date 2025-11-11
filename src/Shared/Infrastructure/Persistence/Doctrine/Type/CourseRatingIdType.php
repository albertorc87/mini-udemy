<?php

declare(strict_types=1);

namespace Udemy\Shared\Infrastructure\Persistence\Doctrine\Type;

use Udemy\Course\Rating\Domain\CourseRatingId;

/**
 * Custom Type para mapear CourseRatingId Value Object
 */
final class CourseRatingIdType extends UlidValueObjectType
{
	public function getName(): string
	{
		return 'course_rating_id';
	}

	protected function getValueObjectClass(): string
	{
		return CourseRatingId::class;
	}
}

