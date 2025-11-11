<?php

declare(strict_types=1);

namespace Udemy\Course\Course\Domain;

use InvalidArgumentException;
use Udemy\Shared\Domain\ValueObject\DecimalValueObject;

final class CoursePrice extends DecimalValueObject
{
	public function __construct(float $value)
	{
		$this->validate($value);
		parent::__construct($value);
	}

	private function validate(float $value): void
	{
		if ($value < 0) {
			throw new InvalidArgumentException(
				sprintf('Course price cannot be negative, got: %f', $value)
			);
		}
	}
}

