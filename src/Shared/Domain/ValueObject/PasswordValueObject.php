<?php

declare(strict_types=1);

namespace Udemy\Shared\Domain\ValueObject;

use InvalidArgumentException;

abstract class PasswordValueObject extends StringValueObject
{
	private const MIN_LENGTH = 8;

	public function __construct(string $value)
	{
		$this->validate($value);
		parent::__construct($value);
	}

	private function validate(string $value): void
	{
		if (strlen($value) < self::MIN_LENGTH) {
			throw new InvalidArgumentException(
				sprintf('Password must be at least %d characters long', self::MIN_LENGTH)
			);
		}
	}
}

