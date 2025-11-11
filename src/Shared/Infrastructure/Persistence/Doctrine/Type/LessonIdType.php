<?php

declare(strict_types=1);

namespace Udemy\Shared\Infrastructure\Persistence\Doctrine\Type;

use Udemy\Course\Lesson\Domain\LessonId;

/**
 * Custom Type para mapear LessonId Value Object
 */
final class LessonIdType extends UlidValueObjectType
{
	public function getName(): string
	{
		return 'lesson_id';
	}

	protected function getValueObjectClass(): string
	{
		return LessonId::class;
	}
}

