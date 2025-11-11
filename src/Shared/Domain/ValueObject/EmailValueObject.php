<?php

declare(strict_types=1);

namespace Udemy\Shared\Domain\ValueObject;

use InvalidArgumentException;

abstract class EmailValueObject extends StringValueObject
{
	public function __construct(string $value)
	{
		$this->validate($value);
		parent::__construct($value);
	}

	private function validate(string $value): void
	{
		if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
			throw new InvalidArgumentException(
				sprintf('Invalid email format: %s', $value)
			);
		}
	}
}

