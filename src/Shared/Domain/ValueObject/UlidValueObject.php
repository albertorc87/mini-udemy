<?php

declare(strict_types=1);

namespace Udemy\Shared\Domain\ValueObject;

use InvalidArgumentException;

abstract class UlidValueObject extends StringValueObject
{
	private const ULID_LENGTH = 26;
	private const ULID_PATTERN = '/^[0-9A-HJKMNP-TV-Z]{26}$/';

	public function __construct(string $value)
	{
		$this->validate($value);
		parent::__construct($value);
	}

	private function validate(string $value): void
	{
		if (strlen($value) !== self::ULID_LENGTH) {
			throw new InvalidArgumentException(
				sprintf('ULID must be exactly %d characters long, got %d', self::ULID_LENGTH, strlen($value))
			);
		}

		if (!preg_match(self::ULID_PATTERN, $value)) {
			throw new InvalidArgumentException(
				sprintf('ULID must contain only alphanumeric characters (0-9, A-Z excluding I, L, O, U), got: %s', $value)
			);
		}
	}

	public function equals(self $other): bool
	{
		return $this->value() === $other->value();
	}
}

