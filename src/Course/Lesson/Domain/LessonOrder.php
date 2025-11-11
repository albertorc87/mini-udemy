<?php

declare(strict_types=1);

namespace Udemy\Course\Lesson\Domain;

use InvalidArgumentException;
use Udemy\Shared\Domain\ValueObject\IntValueObject;

final class LessonOrder extends IntValueObject
{
	public function __construct(int $value)
	{
		$this->validate($value);
		parent::__construct($value);
	}

	private function validate(int $value): void
	{
		if ($value < 0) {
			throw new InvalidArgumentException(
				sprintf('Lesson order cannot be negative, got: %d', $value)
			);
		}
	}
}

