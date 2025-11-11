<?php

declare(strict_types=1);

namespace Udemy\User\User\Domain;

use InvalidArgumentException;
use Udemy\Shared\Domain\ValueObject\StringValueObject;

final class UserPasswordHash extends StringValueObject
{
	private const MAX_LENGTH = 255;

	public function __construct(string $value)
	{
		$this->validateLength($value);
		parent::__construct($value);
	}

	private function validateLength(string $value): void
	{
		if (strlen($value) > self::MAX_LENGTH) {
			throw new InvalidArgumentException(
				sprintf('User password hash cannot exceed %d characters, got %d', self::MAX_LENGTH, strlen($value))
			);
		}
	}
}

