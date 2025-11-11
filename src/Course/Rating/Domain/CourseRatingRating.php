<?php

declare(strict_types=1);

namespace Udemy\Course\Rating\Domain;

use InvalidArgumentException;
use Udemy\Shared\Domain\ValueObject\IntValueObject;

final class CourseRatingRating extends IntValueObject
{
	private const MIN_RATING = 1;
	private const MAX_RATING = 5;

	public function __construct(int $value)
	{
		$this->validate($value);
		parent::__construct($value);
	}

	private function validate(int $value): void
	{
		if ($value < self::MIN_RATING || $value > self::MAX_RATING) {
			throw new InvalidArgumentException(
				sprintf('Course rating must be between %d and %d, got: %d', self::MIN_RATING, self::MAX_RATING, $value)
			);
		}
	}
}

