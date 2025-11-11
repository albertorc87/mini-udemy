<?php

declare(strict_types=1);

namespace Udemy\Course\Course\Domain;

use InvalidArgumentException;
use Udemy\Shared\Domain\ValueObject\DecimalValueObject;

final class CourseAverageRating extends DecimalValueObject
{
	public function __construct(float $value)
	{
		$this->validate($value);
		parent::__construct($value);
	}

	private function validate(float $value): void
	{
		if ($value < 0 || $value > 5) {
			throw new InvalidArgumentException(
				sprintf('Course average rating must be between 0 and 5, got: %f', $value)
			);
		}
	}
}

